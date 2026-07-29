<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CategorieDocumentGenere: string implements HasLabel, HasColor
{
    case Remboursement = 'remboursement';
    case Giac = 'giac';
    case Ofppt = 'ofppt';
    case Entreprise = 'entreprise';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Remboursement => 'Remboursement',
            self::Giac => 'GIAC',
            self::Ofppt => 'OFPPT',
            self::Entreprise => 'Entreprise',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Remboursement => 'info',
            self::Giac => 'warning',
            self::Ofppt => 'success',
            self::Entreprise => 'gray',
        };
    }
}
