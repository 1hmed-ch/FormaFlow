<?php

namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEntrepriseCliente extends EditRecord
{
    protected static string $resource = EntrepriseClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
