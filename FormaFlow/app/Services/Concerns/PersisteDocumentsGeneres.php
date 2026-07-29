<?php

namespace App\Services\Concerns;

use App\Enums\StatutDocumentGenere;
use App\Models\DocumentGenere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Persiste chaque document généré (PDF) à la fois sur le disque configuré
 * et sous forme d'une ligne "documents_generes", afin de constituer un
 * historique/archive consultable (modules "Formation" et "Archives" du
 * cahier des charges) au lieu de se contenter d'un fichier silencieusement
 * écrasé à chaque régénération.
 *
 * Partagé par DocumentGenerationService et GiacDocumentGenerationService
 * pour remplacer les trois méthodes persist()/persistGroupeDocument()/
 * finalize() qui dupliquaient la même logique (TICKET-GIAC-2 / GIAC-3).
 */
trait PersisteDocumentsGeneres
{
    /**
     * Écrit le PDF sur le disque, à un chemin versionné (jamais écrasé),
     * puis enregistre la ligne d'archive correspondante.
     *
     * @param  Model  $documentable  Le modèle auquel rattacher le document
     *         (EntrepriseCliente le plus souvent, Groupe en repli).
     * @param  string  $typeDocument  Une valeur de App\Enums\TypeDocumentGenere.
     * @param  string  $categorie     Une valeur de App\Enums\CategorieDocumentGenere.
     * @param  array<string, mixed>  $metadonnees  Contexte additionnel
     *         (année, ville, groupe_id, theme_id, etude_id...) utile pour
     *         l'affichage sans avoir à recharger les relations.
     */
    protected function finaliserDocument(
        Model $documentable,
        string $typeDocument,
        string $categorie,
        string $filename,
        string $content,
        array $metadonnees = []
    ): DocumentGenere {
        $disk = config('documents.storage_disk', 'local');

        $version = DocumentGenere::query()
                ->where('documentable_type', $documentable->getMorphClass())
                ->where('documentable_id', $documentable->getKey())
                ->where('type_document', $typeDocument)
                ->max('version') + 1;

        $path = sprintf(
            '%s/%s-%d/%s/v%d_%s',
            config('documents.storage_path', 'documents'),
            Str::snake(class_basename($documentable)),
            $documentable->getKey(),
            $categorie,
            $version,
            $filename
        );

        Storage::disk($disk)->put($path, $content);

        return DocumentGenere::create([
            'documentable_type' => $documentable->getMorphClass(),
            'documentable_id' => $documentable->getKey(),
            'type_document' => $typeDocument,
            'categorie' => $categorie,
            'nom_fichier' => $filename,
            'disque' => $disk,
            'chemin' => $path,
            'taille' => strlen($content),
            'version' => $version,
            'statut' => StatutDocumentGenere::Genere,
            'genere_par' => auth()->id(),
            'genere_le' => now(),
            'metadonnees' => $metadonnees,
        ]);
    }
}
