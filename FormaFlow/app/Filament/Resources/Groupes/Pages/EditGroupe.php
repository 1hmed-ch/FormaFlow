<?php

namespace App\Filament\Resources\Groupes\Pages;

use App\Filament\Resources\Groupes\GroupeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGroupe extends EditRecord
{
    protected static string $resource = GroupeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
