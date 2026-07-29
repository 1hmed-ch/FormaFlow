<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatutDocumentGenere: string implements HasLabel, HasColor
{
    case Genere = 'genere';
    case Depose = 'depose';
    case Signe = 'signe';
    //case Remplace = 'remplace';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Genere => 'Généré',
            self::Depose => 'Déposé',
            self::Signe => 'Signé',
            //self::Remplace => 'Remplacé',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Genere => 'violet',
            self::Depose => 'warning',
            self::Signe => 'teal',
            //self::Remplace => 'warning',
        };
    }
}
