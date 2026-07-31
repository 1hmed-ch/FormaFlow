<?php

namespace App\Http\Controllers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaStreamController extends Controller
{
    /**
     * Diffuse un média stocké sur un disk non-public (ex: 'local').
     * Protégée par le middleware 'auth' de la route (voir routes/web.php).
     */
    public function stream(Media $media): StreamedResponse
{
    if (! file_exists($media->getPath())) {
        abort(404, 'Fichier introuvable.');
    }

    return response()->stream(function () use ($media) {
        readfile($media->getPath());
    }, 200, [
        'Content-Type' => $media->mime_type,
        'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
    ]);
}
}