<?php

namespace App\Filament\Resources\EntrepriseClientes\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EntrepriseClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('raison_sociale')
                    ->searchable(),
                TextColumn::make('gerant.nom')
                    ->label('Gérant')
                    ->formatStateUsing(fn ($record) => $record->gerant
                        ? trim($record->gerant->prenom.' '.$record->gerant->nom)
                        : '—')
                    ->searchable(query: fn ($query, string $search) => $query->whereHas(
                        'gerant',
                        fn ($q) => $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                    )),
                TextColumn::make('siege_social')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_creation')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('statut_juridique')
                    ->color(fn (string $state): string => match ($state) {
                        'SARL', 'SARL' => 'info',
                        'SARL AU', 'SARL AU'   => 'warning',
                        'SA', 'SA'   => 'success',
                        'SNC', 'SNC'     => 'danger',
                        default                  => 'gray',
                    })
                    ->badge()
                    ->searchable(),
                TextColumn::make('ice')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('if')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('num_cnss')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rc')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('patente')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('secteur_activite')
                    ->limit(20)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('activite')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('region_affiliation_cnss')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('effectif_total')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fax')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('contact_ref')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('statut_juridique')
                    ->label('Statut Juridique')
                    ->native(false)
                    ->options([
                        'SARL' => 'SARL',
                        'SARL AU' => 'SARL AU',
                        'SA' => 'SA',
                        'SNC' => 'SNC',
                        'Auto-entrepreneur' => 'Auto-entrepreneur',
                    ]),
            ])
            ->recordActions(actions :[
                ActionGroup::make(actions: [
                    ViewAction::make(),
                    EditAction::make(),
                    // Modèle 6 se génère désormais depuis la table des
                    // Formations (une attestation par formation), voir
                    // FormationsTable::configure().
                    DeleteAction::make()
                ])->icon('heroicon-m-ellipsis-vertical')->tooltip('Actions')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
