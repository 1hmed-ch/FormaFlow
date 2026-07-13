<?php

namespace App\Filament\Widgets;

use App\Enums\FormationStatus;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Participant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FormaFlowStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Entreprises Clientes', EntrepriseCliente::count())
                ->icon('heroicon-o-building-office-2')
                ->color('info'),

            Stat::make('Formations en cours', Formation::where('statut', FormationStatus::EN_COURS)->count())
                ->description(Formation::count().' formations au total')
                ->icon('heroicon-o-academic-cap')
                ->color('warning'),

            Stat::make('Participants', Participant::count())
                ->icon('heroicon-o-identification')
                ->color('success'),

            Stat::make('Groupes à capacité pleine', Groupe::withCount('participants')
                ->get()
                ->filter(fn (Groupe $groupe) => $groupe->participants_count >= $groupe->effectif_max)
                ->count())
                ->description(Groupe::count().' groupes au total')
                ->icon('heroicon-o-users')
                ->color('danger'),
        ];
    }
}
