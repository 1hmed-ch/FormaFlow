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

                Section::make('Planification & Statut')
                    ->description('Dates globales et état d\'avancement')
                    ->icon('heroicon-o-calendar')
                    ->columns(2)
                    ->schema([
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
                    ]),
            ]);
    }
}
