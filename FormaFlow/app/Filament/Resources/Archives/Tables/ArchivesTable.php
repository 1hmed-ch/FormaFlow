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

                TextColumn::make('annee_formation')
                    ->label('Année')
                    ->state(fn ($record) => $record->entrepriseCliente?->anneesFormations()[0] ?? '—')
                    ->sortable(false),

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
                    ->options(collect(StatutDossierGiac::cases())
                        ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                        ->toArray()),

                SelectFilter::make('annee')
                    ->label('Année')
                    ->options(fn () => \App\Models\EntrepriseCliente::query()
                        ->with('formations.themes')
                        ->get()
                        ->flatMap(fn ($e) => $e->anneesFormations())
                        ->unique()
                        ->sortDesc()
                        ->mapWithKeys(fn ($annee) => [$annee => $annee])
                        ->toArray())
                    ->query(function ($query, array $data) {
                        if (filled($data['value'])) {
                            $query->whereHas('entrepriseCliente.formations.themes', function ($q) use ($data) {
                                $q->whereYear('date_fin', $data['value']);
                            });
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