<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FormateurStatus: string implements HasLabel
{
    case INTERNE = 'INTERNE';
    case EXTERNE = 'EXTERNE';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INTERNE => 'INTERNE',
            self::EXTERNE => 'EXTERNE',
        };
    }
}
