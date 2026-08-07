<?php

namespace App\Filament\Resources\Formateurs\Tables;

use App\Enums\FormateurStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormateursTable
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
                TextColumn::make('specialite')
                    ->label('Spécialité')
                    ->searchable(),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'INTERNE', 'INTERNE' => 'indigo',
                        'EXTERNE', 'EXTERNE'   => 'orange',
                        default                  => 'gray',
                    }),
                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Adresse Email')
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
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->native(false)
                    ->options(FormateurStatus::class),
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
