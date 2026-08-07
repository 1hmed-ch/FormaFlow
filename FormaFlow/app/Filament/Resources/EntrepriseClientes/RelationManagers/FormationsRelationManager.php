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
use Filament\Forms\Components\ToggleButtons;
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

                /*Select::make('statut')
                    ->label('Statut de la formation')
                    ->options(FormationStatus::class)
                    ->default(FormationStatus::PLANIFIEE)
                    ->native(false)
                    ->required()
                    ->columnSpanFull(),*/
                ToggleButtons::make('statut')
                    ->label('Statut de la formation')
                    ->options(FormationStatus::class)
                    ->default(FormationStatus::PLANIFIEE)
                    ->colors([
                        FormationStatus::PLANIFIEE->value => 'indigo',
                        FormationStatus::EN_COURS->value => 'warning',
                        FormationStatus::TERMINEE->value => 'success',
                        FormationStatus::ANNULEE->value => 'danger',
                    ])
                    ->icons([
                        FormationStatus::PLANIFIEE->value => 'heroicon-o-calendar',
                        FormationStatus::EN_COURS->value => 'heroicon-o-clock',
                        FormationStatus::TERMINEE->value => 'heroicon-o-check-circle',
                        FormationStatus::ANNULEE->value => 'heroicon-o-x-circle',
                    ])
                    ->required()
                    ->columnSpanFull()
                    ->inline(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('intitule')
            ->columns([
                TextColumn::make('intitule')
                    ->label('Intitulé de la formation')
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

                TextColumn::make('statut')
                    ->label('État d\'avancement')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'PLANIFIEE', 'Planifiée' => 'indigo',
                        'EN_COURS', 'En cours'   => 'warning',
                        'TERMINEE', 'Terminée'   => 'success',
                        'ANNULEE', 'Annulée'     => 'danger',
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
                AssociateAction::make(),
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
