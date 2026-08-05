<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DemandeFinancementStatus: string implements HasLabel
{
    case EN_COURS = 'en_cours';
    case ACCEPTEE = 'acceptee';
    case REFUSEE = 'refusee';
    case ARCHIVEE = 'archivee';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EN_COURS => 'En cours',
            self::ACCEPTEE => 'Acceptée',
            self::REFUSEE => 'Refusée',
            self::ARCHIVEE => 'Archivée',
        };
    }
}