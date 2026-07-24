<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TypeFormation: string implements HasLabel
{
    case INGENIERIE = 'ingenierie';
    case DIAGNOSTIC = 'diagnostic';
    case LES_DEUX   = 'les_deux';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INGENIERIE => "Ingénierie de Formation",
            self::DIAGNOSTIC => "Diagnostic Stratégique",
            self::LES_DEUX   => "l'Ingénierie et le Diagnostic",
        };
    }
}