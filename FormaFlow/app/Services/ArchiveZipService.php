<?php

namespace App\Services;

use App\Models\DossierGiac;
use App\Models\EntrepriseFormation;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ArchiveZipService
{
    public function build(DossierGiac $dossier): string
    {
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

        // 1. Pièces jointes de l'entreprise cliente (toutes collections)
        foreach ($entreprise->media as $media) {
            if (file_exists($media->getPath())) {
                $zip->addFile($media->getPath(), 'Entreprise/' . $media->collection_name . '/' . $media->file_name);
            }
        }

        // 2. Documents GIAC générés
        foreach ($entreprise->documentsGeneres as $document) {
            if (Storage::disk($document->disque)->exists($document->chemin)) {
                $zip->addFromString(
                    'GIAC/' . $document->nom_fichier,
                    Storage::disk($document->disque)->get($document->chemin)
                );
            }
        }

        // 3. Pièces jointes de l'organisme de formation (toutes collections)
        foreach ($organisme->media as $media) {
            if (file_exists($media->getPath())) {
                $zip->addFile($media->getPath(), 'Organisme-Formation/' . $media->collection_name . '/' . $media->file_name);
            }
        }

        $zip->close();

        return $zipPath;
    }
}