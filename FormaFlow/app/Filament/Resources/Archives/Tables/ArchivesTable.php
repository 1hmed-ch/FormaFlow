<?php

namespace App\Filament\Resources\Archives\Tables;

use App\Enums\StatutDossierGiac;
use App\Models\DossierGiac;
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
            ->modifyQueryUsing(fn ($query) => $query->with('entrepriseCliente'))
            ->columns([
                TextColumn::make('entrepriseCliente.raison_sociale')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date_generation')
                    ->label('Année')
                    ->state(fn ($record) => $record->date_generation?->year ?? '—')
                    ->sortable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('progression')
                    ->label('Progression')
                    ->state(fn ($record) => $record->getProgressionArchive() . '%')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->getProgressionArchive() >= 100 => 'success',
                        $record->getProgressionArchive() >= 50  => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('entreprise_cliente_id')
                    ->label('Entreprise')
                    ->relationship('entrepriseCliente', 'raison_sociale')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        StatutDossierGiac::EnCours->value => StatutDossierGiac::EnCours->getLabel(),
                        StatutDossierGiac::Signe->value   => StatutDossierGiac::Signe->getLabel(),
                    ]),

                SelectFilter::make('annee')
                    ->label('Année')
                    ->options(fn () => DossierGiac::query()
                        ->selectRaw('YEAR(date_generation) as annee')
                        ->distinct()
                        ->orderByDesc('annee')
                        ->pluck('annee', 'annee')
                        ->filter()
                        ->toArray())
                    ->query(function ($query, array $data) {
                        if (filled($data['value'])) {
                            $query->whereYear('date_generation', $data['value']);
                        }
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