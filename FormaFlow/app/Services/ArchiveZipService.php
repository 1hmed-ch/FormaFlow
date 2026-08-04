<?php

namespace App\Services;

use App\Models\DossierGiac;
use App\Models\EntrepriseFormation;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ArchiveZipService
{
    public function build(
        DossierGiac $dossier,
        ?string $categorie = null,
        ?string $dateDebut = null,
        ?string $dateFin = null
    ): string {
        $entreprise = $dossier->entrepriseCliente;
        $organisme  = EntrepriseFormation::current();

        $zipFilename = sprintf(
            'dossier_%s_%d.zip',
            \Illuminate\Support\Str::slug($entreprise->raison_sociale ?? 'entreprise'),
            $dossier->id
        );
        $zipPath = storage_path('app/tmp/' . $zipFilename);

        if (! is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Impossible de créer l'archive ZIP.");
        }

        // 1. Pièces jointes de l'entreprise cliente
        foreach ($entreprise->media as $media) {
            if (file_exists($media->getPath())) {
                $zip->addFile($media->getPath(), 'Entreprise/' . $media->collection_name . '/' . $media->file_name);
            }
        }

        // 2. Documents générés Triés par les plus récents en premier 
        $documentsQuery = $entreprise->documentsGeneres()->latest('genere_le');

        if (filled($categorie)) {
            $documentsQuery->where('categorie', $categorie);
        }
        if (filled($dateDebut)) {
            $documentsQuery->whereDate('genere_le', '>=', $dateDebut);
        }
        if (filled($dateFin)) {
            $documentsQuery->whereDate('genere_le', '<=', $dateFin);
        }

        // Tableau pour suivre les fichiers déjà ajoutés par leur nom et éviter les doublons obsolètes
        $fichiersAjoutes = [];

        foreach ($documentsQuery->get() as $document) {
            if (Storage::disk($document->disque)->exists($document->chemin)) {
                $sousDossier = $document->categorie?->value ?? $document->categorie ?? 'Autres';
                $cheminDansZip = ucfirst($sousDossier) . '/' . $document->nom_fichier;

                // Si le fichier avec ce nom n'a pas encore été ajouté, on l'ajoute.
                // Comme la requête est triée par ordre décroissant (latest), 
                // la première occurrence est forcément la plus récente !
                if (!in_array($cheminDansZip, $fichiersAjoutes)) {
                    $zip->addFromString(
                        $cheminDansZip,
                        Storage::disk($document->disque)->get($document->chemin)
                    );
                    
                    $fichiersAjoutes[] = $cheminDansZip;
                }
            }
        }

        // 3. Pièces jointes de l'organisme de formation
        foreach ($organisme->media as $media) {
            if (file_exists($media->getPath())) {
                $zip->addFile($media->getPath(), 'Organisme-Formation/' . $media->collection_name . '/' . $media->file_name);
            }
        }

        $zip->close();

        return $zipPath;
    }
}