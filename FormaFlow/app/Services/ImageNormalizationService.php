<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\PngEncoder;

class ImageNormalizationService
{
    /**
     * Normalise l'image d'entête sur un canevas fixe (logo).
     */
    public function normalizeEntete(UploadedFile $file, string $disk = 'public'): string
    {
        return $this->normalize($file, 600, 150, 'entetes', $disk);
    }

    /**
     * Normalise l'image de pied de page sur un canevas fixe.
     */
    public function normalizePiedPage(UploadedFile $file, string $disk = 'public'): string
    {
        return $this->normalize($file, 1200, 100, 'pieds-page', $disk);
    }

    protected function normalize(UploadedFile $file, int $width, int $height, string $folder, string $disk): string
    {
        $img = Image::read($file)
            ->scaleDown(width: $width, height: $height)
            ->resizeCanvas($width, $height, background: 'ffffff')
            ->encode(new PngEncoder());

        $path = sprintf('%s/%s.png', $folder, Str::uuid());

        Storage::disk($disk)->put($path, (string) $img);

        return $path;
    }

    /**
     * Supprime l'ancienne image si elle existe (à appeler avant de stocker la nouvelle,
     * utile lors d'une mise à jour).
     */
    public function deleteIfExists(?string $path, string $disk = 'public'): void
    {
        if (!empty($path) && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}