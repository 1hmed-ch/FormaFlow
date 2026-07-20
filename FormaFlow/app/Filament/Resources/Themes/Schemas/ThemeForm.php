<?php

namespace App\Filament\Resources\Themes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;

class ThemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détails du Thème')
                    ->description('Informations pédagogiques et durée du module')
                    ->icon('heroicon-o-book-open')
                    ->columns(2)
                    ->schema([
                        TextInput::make('intitule')
                            ->label('Intitulé du thème')
                            ->placeholder('Ex: Architecture Microservices, Management Agile...')
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
                        Textarea::make('objectifs')
                            ->label('Objectifs pédagogiques')
                            ->placeholder('Décrivez les compétences visées par ce thème...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Rattachements & Affectation')
                    ->description('Lien avec la formation globale et le formateur assigné')
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        Select::make('formation_id')
                            ->label('Formation parente')
                            ->relationship('formation', 'intitule')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('formateur_id')
                            ->label('Formateur assigné')
                            ->relationship('formateur', 'nom')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['nom', 'prenom'])
                            ->preload()
                            ->required(),
                    ])->columnSpanFull(),
            ]);
    }
}
