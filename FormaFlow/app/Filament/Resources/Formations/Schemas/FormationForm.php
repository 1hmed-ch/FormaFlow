<?php

namespace App\Filament\Resources\Formations\Schemas;

use App\Enums\FormationStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\TypeFormation;

class FormationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détails de la Formation')
                    ->description('Informations principales et rattachement client')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        TextInput::make('intitule')
                            ->label('Intitulé de la formation')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        DatePicker::make('date_debut')
                            ->label('Date de début')
                            ->native(false),

                        DatePicker::make('date_fin')
                            ->label('Date de fin')
                            ->native(false)
                            ->afterOrEqual('date_debut'),

                        Select::make('entreprise_id')
                            ->label('Entreprise Cliente')
                            ->relationship('entrepriseCliente', 'raison_sociale')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Select::make('type_formation')
                            ->label('Type d\'Étude / Formation')
                            ->options(TypeFormation::class)
                            ->required()
                            ->native(false)
                            ->searchable(),
                        /*Select::make('statut')
                            ->label('Statut de la formation')
                            ->options(FormationStatus::class)
                            ->default('Planifiee')
                            ->native(false)
                            ->required(),*/
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
                            ->inline()
                            ->columnSpanFull(),

                    ])->columnSpanFull(),
            ]);
    }
}
