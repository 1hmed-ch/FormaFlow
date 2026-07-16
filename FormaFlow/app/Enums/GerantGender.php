<?php

namespace App\Enums;

use Filament\Actions\Concerns\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum GerantGender : string implements \Filament\Support\Contracts\HasLabel
{
    case Homme = "Homme";
    case Femme = "Femme";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Homme => 'Homme',
            self::Femme => 'Femme',
        };
    }
}
