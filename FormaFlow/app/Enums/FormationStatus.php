<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FormationStatus: string implements HasLabel
{
    case PLANIFIEE = 'Planifiee';
    case EN_COURS = 'En cours';
    case TERMINEE = 'Terminee';
    case ANNULEE = 'Annulee';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PLANIFIEE => 'Planifiee',
            self::EN_COURS  => 'En cours',
            self::TERMINEE  => 'Terminee',
            self::ANNULEE   => 'Annulee',
        };
    }
}