<?php

namespace App\Filament\Resources\Archives\Tables;

use App\Enums\FormationStatus;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArchivesTable
{
    public static function configure(Table $table): Table
    {
        return $table
           ->modifyQueryUsing(fn ($query) => $query
            ->whereNull('formation_id')
            ->with('entrepriseCliente'))
            ->columns([
                TextColumn::make('entrepriseCliente.raison_sociale')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('annee_formation')
                    ->label('Année')
                    ->state(fn ($record) => $record->entrepriseCliente?->anneeArchive() ?? '—')
                    ->sortable(false),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->state(fn ($record) => $record->entrepriseCliente?->statutDerniereFormation())
                    ->badge()
                    ->color(fn (?FormationStatus $state) => match ($state) {
                        FormationStatus::PLANIFIEE => 'indigo',
                        FormationStatus::EN_COURS  => 'warning',
                        FormationStatus::TERMINEE  => 'success',
                        FormationStatus::ANNULEE   => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('entreprise_cliente_id')
                    ->label('Entreprise')
                    ->relationship('entrepriseCliente', 'raison_sociale')
                    ->searchable()
                    ->preload(),

                // Filtre sur le statut de la DERNIÈRE formation de l'entreprise
                // (même logique que la colonne "Statut" / statutDerniereFormation()).
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(collect(FormationStatus::cases())
                        ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                        ->toArray())
                    ->query(function ($query, array $data) {
                        if (blank($data['value'])) {
                            return;
                        }

                        $query->whereHas('entrepriseCliente.formations', function ($q) use ($data) {
                            $q->where('statut', $data['value'])
                                ->where('date_debut', function ($sub) {
                                    $sub->selectRaw('MAX(date_debut)')
                                        ->from('formations as f_last')
                                        ->whereColumn('f_last.entreprise_id', 'formations.entreprise_id');
                                });
                        });
                    })
                    ->indicateUsing(function (array $data): ?Indicator {
                        if (blank($data['value'])) {
                            return null;
                        }

                        $label = FormationStatus::tryFrom($data['value'])?->getLabel() ?? $data['value'];

                        return Indicator::make('Statut : ' . $label)
                            ->removeField('value');
                    }),

                // Filtre sur l'année de la PREMIÈRE formation de l'entreprise
                // (même logique que la colonne "Année" / anneeArchive()).
                SelectFilter::make('annee')
                    ->label('Année')
                    ->options(fn () => \App\Models\EntrepriseCliente::query()
                        ->whereHas('formations')
                        ->get()
                        ->map(fn ($e) => $e->anneeArchive())
                        ->filter()
                        ->unique()
                        ->sortDesc()
                        ->mapWithKeys(fn ($annee) => [$annee => $annee])
                        ->toArray())
                    ->query(function ($query, array $data) {
                        if (blank($data['value'])) {
                            return;
                        }

                        $query->whereHas('entrepriseCliente.formations', function ($q) use ($data) {
                            $q->whereYear('date_debut', $data['value'])
                                ->where('date_debut', function ($sub) {
                                    $sub->selectRaw('MIN(date_debut)')
                                        ->from('formations as f_first')
                                        ->whereColumn('f_first.entreprise_id', 'formations.entreprise_id');
                                });
                        });
                    })
                    ->indicateUsing(function (array $data): ?Indicator {
                        if (blank($data['value'])) {
                            return null;
                        }

                        return Indicator::make('Année : ' . $data['value'])
                            ->removeField('value');
                    }),
            ])
            ->recordActions([
                Action::make('voir')
                    ->label('Voir le dossier')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.archives.view', $record)),
            ]);
    }
}