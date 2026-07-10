<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FormateurStatus: string implements HasLabel
{
    case INTERNE = 'INTERNE';
    case EXTERNE = 'EXTERNE';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INTERNE => 'INTERNE',
            self::EXTERNE => 'EXTERNE',
        };
    }
}