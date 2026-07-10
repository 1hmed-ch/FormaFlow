<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategorieSP: string implements HasLabel
{
    case Cadre = 'C';
    case Employe = 'E';
    case Ouvrier = 'O';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cadre   => 'C',
            self::Employe => 'E',
            self::Ouvrier => 'O',
        };
    }
}