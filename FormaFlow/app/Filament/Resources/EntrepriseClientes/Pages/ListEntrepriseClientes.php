<?php

namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntrepriseClientes extends ListRecords
{
    protected static string $resource = EntrepriseClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Créer une Entreprise'),
        ];
    }
}
