<?php

namespace App\Filament\Resources\Groupes\Tables;

use App\Exceptions\DocumentGenerationException;
use App\Models\Groupe;
use App\Services\DocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;

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
                    ->native(false)
                    ->relationship('theme', 'intitule')
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
            ->recordActions(actions: [
                ActionGroup::make(actions: [
                    EditAction::make(),

                    Action::make('genererFichePresence')
                        ->label('Fiche de présence')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function (Groupe $record, Action $action) {
                            try {
                                $document = app(DocumentGenerationService::class)
                                    ->generateFichePresence($record);

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
                        ->action(function (Groupe $record, array $data, Action $action) {
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
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions')
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
