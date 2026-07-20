<?php

namespace App\Filament\Resources\Groupes\Schemas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GroupeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du Groupe')
                    ->description('Configuration générale et rattachement au thème')
                    ->icon('heroicon-o-users')
                    ->columns(2)
                    ->schema([
                        TextInput::make('libelle')
                            ->label('Libellé du groupe')
                            ->placeholder('Ex: Groupe A, Session Matin...')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('effectif_max')
                            ->label('Effectif maximal')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Select::make('theme_id')
                            ->label('Thème de formation')
                            ->relationship('theme', 'intitule')
                            ->searchable()
                            ->columnSpanFull()
                            ->preload()
                            ->required(),
                        TextInput::make('lieu')
                            ->label('Lieu de formation')
                            ->placeholder('Ex: Salle de conférence principale, En ligne...')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
