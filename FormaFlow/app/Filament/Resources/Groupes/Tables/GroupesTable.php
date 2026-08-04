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
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

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
                    ->badge()
                    ->color('indigo')
                    ->icon('heroicon-o-users')
                    ->sortable(),
                TextColumn::make('lieu')
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
                SelectFilter::make('theme_id')
                    ->label('Thème')
                    ->native(false)
                    ->relationship('theme', 'intitule')
                    ->searchable()
                    ->preload(),
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
