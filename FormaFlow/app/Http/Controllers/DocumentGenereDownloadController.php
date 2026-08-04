<?php

namespace App\Http\Controllers;

use App\Models\DocumentGenere;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Permet de re-télécharger un document déjà généré, à partir de sa ligne
 * d'archive (documents_generes), sans avoir à le régénérer.
 *
 * Volontairement une route/contrôleur "classique" plutôt qu'une Action
 * Filament ->action(...streamDownload...) : le fichier existe déjà sur le
 * disque, un lien direct évite de faire transiter le contenu binaire par
 * le cycle de requête Livewire (utile pour des PDF volumineux).
 */
class DocumentGenereDownloadController extends Controller
{
    public function __invoke(DocumentGenere $documentGenere): StreamedResponse
    {
        abort_unless(
            $documentGenere->existeSurLeDisque(),
            404,
            "Ce document n'est plus disponible sur le disque de stockage."
        );

        return Storage::disk($documentGenere->disque)->download(
            $documentGenere->chemin,
            $documentGenere->nom_fichier
        );
    }
    public function stream(DocumentGenere $documentGenere): StreamedResponse
    {
        abort_unless(
            $documentGenere->existeSurLeDisque(),
            404,
            "Ce document n'est plus disponible sur le disque de stockage."
        );

        return Storage::disk($documentGenere->disque)->response(
            $documentGenere->chemin,
            $documentGenere->nom_fichier,
            ['Content-Type' => 'application/pdf']
        );
    }
}
