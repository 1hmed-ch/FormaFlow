<?php

namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Enums\CategorieDocumentGenere;
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

    /**
     * État du filtre appliqué à la section "Archive des documents générés"
     * de l'infolist. Lu et modifié depuis EntrepriseClienteInfolist via
     * l'action "Filtrer" (catégorie + plage de dates de génération).
     */
    public string|CategorieDocumentGenere|null $archiveDocumentsCategorie = null;

    public ?string $archiveDocumentsDateDebut = null;

    public ?string $archiveDocumentsDateFin = null;

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
