<?php
namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentImageNormalizer
{
    // dims en px, x2 pour garder du piqué à l'impression (on affichera à moitié en CSS)
    private const ENTETE_W = 480;
    private const ENTETE_H = 170;
    private const FOOTER_W = 700;
    private const FOOTER_H = 110;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function normalizeEntete(UploadedFile $file): string
    {
        return $this->normalize($file, self::ENTETE_W, self::ENTETE_H, 'entreprises/entetes');
    }

    public function normalizePiedPage(UploadedFile $file): string
    {
        return $this->normalize($file, self::FOOTER_W, self::FOOTER_H, 'entreprises/pieds-page');
    }

    private function normalize(UploadedFile $file, int $w, int $h, string $dir): string
    {
        $img = $this->manager->read($file->getRealPath());
        $img->scaleDown($w, $h); // redimensionne SANS déformer, garde le ratio

        $canvas = $this->manager->create($w, $h)->fill('ffffff');
        $canvas->place($img, 'center'); // centre l'image sur le canvas blanc

        $path = $dir.'/'.Str::uuid().'.png';
        $canvas->toPng()->save(storage_path('app/public/'.$path));

        return $path;
    }
}