<?php

namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateEntrepriseCliente extends CreateRecord
{
    protected static string $resource = EntrepriseClienteResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Créer l\'Entreprise');
    }
}
