<?php

namespace App\Filament\Resources\Formations\Schemas;

use App\Enums\FormationStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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

                        Select::make('entreprise_id')
                            ->label('Entreprise Cliente')
                            ->relationship('entrepriseCliente', 'raison_sociale')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Statut de la Formation')
                        ->icon('heroicon-o-arrow-path')
                        ->schema([
                         Select::make('statut')
                             ->label('Statut de la formation')
                             ->options(FormationStatus::class)
                            ->default('Planifiee')
                            ->native(false)
                            ->required(),
        
                        ]),

            ]);
    }
}
