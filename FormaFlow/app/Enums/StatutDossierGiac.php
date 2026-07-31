<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatutDossierGiac: string implements HasLabel , HasColor
{
    case EnCours = 'en_cours';
    case Signe = 'signe';
   

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EnCours => 'En cours',
            self::Signe  => 'Signé',
           
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EnCours => 'warning',
            self::Signe   => 'success',
        };
    }

    
}