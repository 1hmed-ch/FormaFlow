<?php

namespace App\Filament\Resources\Groupes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\DocumentGenerationException;
use App\Services\DocumentGenerationService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class GroupesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('libelle')
                    ->searchable(),
                TextColumn::make('theme.intitule')
                    ->label('Thème')
                    ->searchable(),
                TextColumn::make('effectif_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lieu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_debut')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_fin')
                    ->date()
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
                SelectFilter::make('theme_id')
                    ->label('Thème')
                    ->relationship('theme', 'intitule')
                    ->searchable()
                    ->preload(),

                Filter::make('date_debut')
                    ->schema([
                        DatePicker::make('debut_from')
                            ->label('Débute après le'),
                        DatePicker::make('debut_until')
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
                 Action::make('genererFicheEvaluation')
                    ->label("Fiche d'évaluation")
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->form([
                        TextInput::make('ville')
                            ->label('Ville')
                            ->required()
                            ->maxLength(255),
                    ])
            ->action(function (\App\Models\Groupe $record, array $data, Action $action) {
                    try {
                        $document = app(DocumentGenerationService::class)
                            ->generateFicheEvaluation($record, $data['ville']);

                        return response()->streamDownload(
                            function () use ($document) {
                                echo $document['content'];
                            },
                            $document['filename'],
                            ['Content-Type' => 'application/pdf']
                        );
                    } catch (DocumentGenerationException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Génération impossible')
                            ->body($e->getMessage())
                            ->send();

                        $action->halt();
                    }
                }),
            DeleteAction::make(),
        ])
                    
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
