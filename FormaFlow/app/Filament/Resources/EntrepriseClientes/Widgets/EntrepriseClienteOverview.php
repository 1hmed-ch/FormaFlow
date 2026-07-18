<?php

namespace App\Filament\Resources\EntrepriseClientes\Widgets;

use App\Enums\FormationStatus;
use App\Models\EntrepriseCliente;
use App\Models\Groupe;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Storage;

class EntrepriseClienteOverview extends BaseWidget
{
    public ?EntrepriseCliente $record = null;
    protected int|string|array $columnSpan = 1;

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        $entreprise = $this->record;

        $formationsEnCours = $entreprise->formations()
            ->where('statut', FormationStatus::EN_COURS)
            ->count();

        $formationsTerminees = $entreprise->formations()
            ->where('statut', FormationStatus::TERMINEE)
            ->count();

        $groupesCount = Groupe::whereHas(
            'theme.formation',
            fn ($query) => $query->where('entreprise_id', $entreprise->id)
        )->count();

        $disk = Storage::disk(config('documents.storage_disk', 'local'));
        $documentsDir = config('documents.storage_path', 'documents').'/entreprise-'.$entreprise->id;
        $documentsCount = $disk->directoryExists($documentsDir) ? count($disk->allFiles($documentsDir)) : 0;

        return [
            Stat::make('Formations', $entreprise->formations()->count())
                ->description($formationsEnCours.' en cours · '.$formationsTerminees.' terminées')
                ->icon('heroicon-o-academic-cap')
                ->color('info'),

            Stat::make('Participants', $entreprise->participants()->count())
                ->icon('heroicon-o-identification')
                ->color('success'),

            Stat::make('Groupes', $groupesCount)
                ->description('Toutes formations confondues')
                ->icon('heroicon-o-user-group')
                ->color('warning'),

            Stat::make('Documents générés', $documentsCount)
                ->description('Modèles 6 archivés')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray'),
        ];
    }
}
