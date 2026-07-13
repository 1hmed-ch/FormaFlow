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
                    ->searchable(),
                TextColumn::make('prenom')
                    ->searchable(),
                TextColumn::make('cin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email address')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('numero_cnss')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fonction_occupee')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('categorie_sp')
                    ->label('Categorie')
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'Ouvrier', 'O' => 'info',
                        'Cadre', 'C'   => 'warning',
                        'Employe', 'E'   => 'success',
                        default                  => 'gray',
                    })
                    ->badge(),
                TextColumn::make('entreprise.raison_sociale')
                    ->label('Entreprise Client')
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
                    ->options(CategorieSP::class),

                SelectFilter::make('entreprise_id')
                    ->label('Entreprise Cliente')
                    ->relationship('entreprise', 'raison_sociale')
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
