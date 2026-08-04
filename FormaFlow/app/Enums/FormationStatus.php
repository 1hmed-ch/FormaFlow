<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FormationStatus: string implements HasLabel
{
    case PLANIFIEE = 'Planifiée';
    case EN_COURS = 'En cours';
    case TERMINEE = 'Terminée';
    case ANNULEE = 'Annulée';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PLANIFIEE => 'Planifiée',
            self::EN_COURS  => 'En cours',
            self::TERMINEE  => 'Terminée',
            self::ANNULEE   => 'Annulée',
        };
    }
}
