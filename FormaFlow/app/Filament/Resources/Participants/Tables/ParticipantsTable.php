<?php

namespace App\Filament\Resources\Participants\Tables;

use App\Enums\CategorieSP;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable(),
                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable(),
                TextColumn::make('cin')
                    ->label('CIN')
                    ->searchable()
                    /*->toggleable(isToggledHiddenByDefault: true)*/,
                TextColumn::make('email')
                    ->label('Adresse Email')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('numero_cnss')
                    ->label('N° CNSS')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fonction_occupee')
                    ->label('Fonction occupée')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('categorie_sp')
                    ->label('Catégorie')
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'Ouvrier', 'O' => 'indigo',
                        'Cadre', 'C'   => 'warning',
                        'Employe', 'E'   => 'success',
                        default                  => 'gray',
                    })
                    ->badge(),
                TextColumn::make('entrepriseCliente.raison_sociale')
                    ->label('Entreprise Cliente')
                    ->limit(35)
                    ->searchable(),
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
                SelectFilter::make('categorie_sp')
                    ->label('Catégorie')
                    ->native(false)
                    ->options(CategorieSP::class),

                SelectFilter::make('entreprise_id')
                    ->label('Entreprise Cliente')
                    ->native(false)
                    ->relationship('entrepriseCliente', 'raison_sociale')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
