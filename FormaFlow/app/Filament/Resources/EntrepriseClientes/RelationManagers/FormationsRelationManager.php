<?php

namespace App\Filament\Resources\EntrepriseClientes\RelationManagers;

use App\Enums\FormationStatus;
use App\Exceptions\DocumentGenerationException;
use App\Models\Formation;
use App\Services\DocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormationsRelationManager extends RelationManager
{
    protected static string $relationship = 'formations';

    protected static ?string $title = 'Formations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('intitule')
                    ->label('Intitulé de la formation')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                DatePicker::make('date_debut')
                    ->label('Date de début')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->required(),

                DatePicker::make('date_fin')
                    ->label('Date de fin')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->afterOrEqual('date_debut')
                    ->required(),

                Select::make('statut')
                    ->label('Statut de la formation')
                    ->options(FormationStatus::class)
                    ->default('Planifiee')
                    ->native(false)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('intitule')
            ->columns([
                TextColumn::make('intitule')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('date_debut')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('date_fin')
                    ->date('d/m/Y')
                    ->sortable(),

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
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(FormationStatus::class),
            ])
            ->headerActions([
                CreateAction::make(),
                // "entreprise_id" is NOT NULL, so Associate reassigns an
                // existing formation from another entreprise rather than
                // linking an "orphan" one.
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                /*Action::make('genererModele6')
                    ->label('Modèle 6')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->visible(fn (Formation $record): bool => $record->statut === FormationStatus::TERMINEE)
                    ->form([
                        TextInput::make('annee')
                            ->label('Exercice (année)')
                            ->numeric()
                            ->required()
                            ->default(now()->year)
                            ->minValue(2000),
                    ])
                    ->action(function (Formation $record, array $data, Action $action) {
                        try {
                            $document = app(DocumentGenerationService::class)
                                ->generateModele6($record, (int) $data['annee']);

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
                    }),*/
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
