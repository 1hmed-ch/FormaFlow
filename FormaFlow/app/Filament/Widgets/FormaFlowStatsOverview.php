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

            Stat::make('Nombre de Formations', Formation::count())
                ->description(Formation::where('statut', FormationStatus::TERMINEE)->count().' formations terminées')
                ->icon('heroicon-o-academic-cap')
                ->color('success'),

            Stat::make('Participants', Participant::count())
                ->icon('heroicon-o-identification')
                ->color('success'),
        ];
    }
}
