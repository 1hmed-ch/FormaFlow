<?php

namespace App\Filament\Resources\EntrepriseClientes\Widgets;

use App\Enums\FormationStatus;
use App\Filament\Resources\Archives\ArchiveResource;
use App\Filament\Resources\Formations\FormationResource;
use App\Filament\Resources\Groupes\GroupeResource;
use App\Filament\Resources\Participants\ParticipantResource;
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
                ->url(FormationResource::getUrl('index'))
                ->color('info'),

            Stat::make('Participants', $entreprise->participants()->count())
                ->icon('heroicon-o-identification')
                ->url(ParticipantResource::getUrl('index'))
                ->color('success'),

            Stat::make('Groupes', $groupesCount)
                ->description('Toutes formations confondues')
                ->icon('heroicon-o-user-group')
                ->url(GroupeResource::getUrl('index'))
                ->color('warning'),

            Stat::make('Documents générés', $documentsCount)
                ->description('Modèles 6 archivés')
                ->icon('heroicon-o-document-arrow-down')
                ->url(ArchiveResource::getUrl('view', ['record' => $this->record]))
                ->color('gray'),
        ];
    }
}
