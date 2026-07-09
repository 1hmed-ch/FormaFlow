<?php

namespace App\Filament\Resources\Themes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ThemesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('intitule')
                    ->label('Thème')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('duree_prevue')
                    ->label('Durée (H)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('formation.intitule')
                    ->label('Formation')
                    ->limit(35)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('formateur.full_name')
                    ->label('Formateur')
                    ->searchable(['nom', 'prenom'])
                    ->sortable(['nom', 'prenom']),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
