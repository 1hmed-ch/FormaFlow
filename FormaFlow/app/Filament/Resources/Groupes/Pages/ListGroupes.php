<?php

namespace App\Filament\Resources\Groupes\Pages;

use App\Filament\Resources\Groupes\GroupeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGroupes extends ListRecords
{
    protected static string $resource = GroupeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
