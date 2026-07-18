<?php

namespace App\Filament\Resources\Formations\Tables;

use App\Enums\FormationStatus;
use App\Models\Formation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('intitule')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('statut')
                    ->label('État d\'avancement')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'PLANIFIEE', 'Planifiee' => 'info',
                        'EN_COURS', 'En cours'   => 'warning',
                        'TERMINEE', 'Terminee'   => 'success',
                        'ANNULEE', 'Annulee'     => 'danger',
                        default                  => 'gray',
                    }),
                TextColumn::make('entrepriseCliente.raison_sociale')
                    ->label('Entreprise Cliente')
                    ->limit(35)
                    ->searchable()
                    ->sortable(),
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
                    ->label('État d\'avancement')
                    ->native(false)
                    ->options(FormationStatus::class),

                SelectFilter::make('entreprise_id')
                    ->label('Entreprise Cliente')
                    ->native(false)
                    ->relationship('entrepriseCliente', 'raison_sociale')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('changerStatut')
                    ->label('Changer le statut')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('statut')
                            ->label('État d\'avancement')
                            ->options(FormationStatus::class)
                            ->native(false)
                            ->default(fn (Formation $record) => $record->statut)
                            ->required(),
                    ])
                    ->action(function (Formation $record, array $data): void {
                        $record->update([
                            'statut' => $data['statut']
                        ]);
                    })
                    ->successNotificationTitle('Statut mis à jour avec succès.'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
