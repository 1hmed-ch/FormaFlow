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
                    ->label('Raison sociale')
                    ->sortable()
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
                    ->label('Siège social')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_creation')
                    ->label('Date de création')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('statut_juridique')
                    ->label('Statut juridique')
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
                    ->label('ICE')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('if')
                    ->label('IF')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('num_cnss')
                    ->label('Numéro CNSS')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rc')
                    ->label('RC')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('patente')
                    ->label('Patente')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('secteur_activite')
                    ->label('Secteur d\'activité')
                    ->limit(20)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('activite')
                    ->label('Activité')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('region_affiliation_cnss')
                    ->label('Région d\'affiliation CNSS')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('effectif_total')
                    ->label('Effectif total')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fax')
                    ->label('Fax')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Adresse Email')
                    ->searchable(),
                TextColumn::make('contact_ref')
                    ->label('Contact de référence')
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
