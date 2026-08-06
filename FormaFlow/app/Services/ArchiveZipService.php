<?php

namespace App\Services;

use App\Models\DossierGiac;
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

        // Documents générés, triés par les plus récents en premier
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

                if (!in_array($cheminDansZip, $fichiersAjoutes)) {
                    $zip->addFromString(
                        $cheminDansZip,
                        Storage::disk($document->disque)->get($document->chemin)
                    );

                    $fichiersAjoutes[] = $cheminDansZip;
                }
            }
        }

        if (empty($fichiersAjoutes)) {
            $zip->close();
            @unlink($zipPath);

            throw new \RuntimeException(
                "Aucun document ne correspond aux critères sélectionnés : l'archive serait vide."
            );
        }

        $zip->close();

        return $zipPath;
    }
}
