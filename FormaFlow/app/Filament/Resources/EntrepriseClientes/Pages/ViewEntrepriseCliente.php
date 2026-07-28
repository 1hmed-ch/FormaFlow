<?php

namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Exceptions\DocumentGenerationException;
use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use App\Filament\Resources\EntrepriseClientes\Widgets\EntrepriseClienteFormationsChart;
use App\Filament\Resources\EntrepriseClientes\Widgets\EntrepriseClienteOverview;
use App\Models\EntrepriseCliente;
use App\Services\DocumentGenerationService;
use App\Services\GiacDocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEntrepriseCliente extends ViewRecord
{
    protected static string $resource = EntrepriseClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [            
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EntrepriseClienteOverview::class,
            EntrepriseClienteFormationsChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}