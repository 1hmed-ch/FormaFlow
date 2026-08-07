<?php

namespace App\Filament\Resources\Themes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('date_debut')
                    ->label('Date de début')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('date_fin')
                    ->label('Date de fin')
                    ->date('d/m/Y')
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
                SelectFilter::make('formation_id')
                    ->label('Formation')
                    ->native(false)
                    ->relationship('formation', 'intitule')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('formateur_id')
                    ->label('Formateur')
                    ->native(false)
                    ->relationship('formateur', 'nom')
                    ->searchable()
                    ->preload(),
                Filter::make('date_debut')
                    ->schema([
                        DatePicker::make('debut_from')
                            ->native(false)
                            ->label('Débute après le'),
                        DatePicker::make('debut_until')
                            ->native(false)
                            ->label('Débute avant le'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['debut_from'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('date_debut', '>=', $date),
                            )
                            ->when(
                                $data['debut_until'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('date_debut', '<=', $date),
                            );
                    }),
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
