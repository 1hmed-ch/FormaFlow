<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Formations\FormationResource;
use App\Models\Formation;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class FormationsCalendarWidget extends FullCalendarWidget
{
    public ?string $periodeDebut = null;

    public ?string $periodeFin = null;

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'displayEventTime' => false,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,listMonth',
            ],
        ];
    }


    protected function headerActions(): array
    {
        return [
            Action::make('filtrerPeriode')
                ->label($this->hasPeriodeFiltree() ? 'Période filtrée' : 'Filtrer par période')
                ->icon('heroicon-o-funnel')
                ->color($this->hasPeriodeFiltree() ? 'success' : 'gray')
                ->schema([
                    Grid::make(2)->schema([
                        DatePicker::make('periode_debut')
                            ->label('Du')
                            ->placeholder('Date de début')
                            ->native(false)
                            ->default($this->periodeDebut),
                        DatePicker::make('periode_fin')
                            ->label('Au')
                            ->placeholder('Date de fin')
                            ->native(false)
                            ->default($this->periodeFin)
                            ->afterOrEqual('periode_debut'),
                    ]),
                ])
                ->action(function (array $data): void {
                    $this->periodeDebut = $data['periode_debut'] ?? null;
                    $this->periodeFin = $data['periode_fin'] ?? null;

                    if ($this->periodeDebut) {
                        $this->dispatch('filament-fullcalendar--goto', date: $this->periodeDebut);
                    }

                    $this->refreshRecords();
                }),

            Action::make('reinitialiserPeriode')
                ->label('Réinitialiser')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->visible(fn (): bool => $this->hasPeriodeFiltree())
                ->action(function (): void {
                    $this->periodeDebut = null;
                    $this->periodeFin = null;
                    $this->refreshRecords();
                }),
        ];
    }

    public function fetchEvents(array $info): array
    {
        return Formation::query()
            /*->terminees()*/
            ->dansPeriode($this->periodeDebut, $this->periodeFin)
            ->where('date_debut', '<=', $info['end'])
            ->where('date_fin', '>=', $info['start'])
            ->whereNotNull('date_debut')
            ->with('entrepriseCliente')
            ->get()
            ->map(function (Formation $formation) {
                $fin = ($formation->date_fin ?? $formation->date_debut)->copy()->addDay();

                $entrepriseName = $formation->entrepriseCliente ? $formation->entrepriseCliente->raison_sociale : 'Sans Entreprise';

                $eventColor = match (strtolower($formation->statut->value)) {
                    'terminée' => '#90B800',
                    'planifiée' => '#293681',
                    'en cours' => '#FF8F00',
                    'annulée' => '#D90000',
                    default => '#4B5694',
                };

                return EventData::make()
                    ->id($formation->id)
                    ->title($formation->intitule.' ( '.$entrepriseName.' ) ')
                    ->start($formation->date_debut)
                    ->end($fin)
                    ->textColor('#FFFFFF')
                    ->backgroundColor($eventColor) //#4B5694
                    ->borderColor($eventColor)
                    ->url(FormationResource::getUrl('edit', ['record' => $formation]), shouldOpenUrlInNewTab: true);
            })
            ->toArray();
    }

    protected function hasPeriodeFiltree(): bool
    {
        return filled($this->periodeDebut) || filled($this->periodeFin);
    }
}
