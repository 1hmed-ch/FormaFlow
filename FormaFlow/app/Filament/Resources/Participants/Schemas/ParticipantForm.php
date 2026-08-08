<?php

namespace App\Filament\Resources\Participants\Schemas;

use App\Enums\CategorieSP;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité de l\'Employé')
                    ->description('Informations personnelles et identification légale')
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Select::make('entreprise_id')
                            ->label('Entreprise Cliente')
                            ->relationship('entrepriseCliente', 'raison_sociale')
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cin')
                            ->label('CIN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        TextInput::make('numero_cnss')
                            ->label('Numéro CNSS')
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        TextInput::make('fonction_occupee')
                            ->label('Fonction occupée')
                            ->columnSpanFull()
                            ->maxLength(255),

                        ToggleButtons::make('categorie_sp')
                            ->label('Catégorie Socio-Professionnelle')
                            ->options([
                                CategorieSP::Cadre->value => 'Cadre',
                                CategorieSP::Employe->value => 'Employé',
                                CategorieSP::Ouvrier->value => 'Ouvrier',
                            ])
                            ->colors([
                                CategorieSP::Cadre->value => 'primary',
                                CategorieSP::Employe->value => 'teal',
                                CategorieSP::Ouvrier->value => 'indigo',
                            ])
                            ->icons([
                                CategorieSP::Cadre->value => 'heroicon-o-user-circle',
                                CategorieSP::Employe->value => 'heroicon-o-user-group',
                                CategorieSP::Ouvrier->value => 'heroicon-o-user',
                            ])
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Coordonnées')
                    ->description('Moyens de contact du participant')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('email')
                            ->label('Adresse Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->placeholder("saisissez l'adresse email")
                            ->maxLength(255),

                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+212 6 00 00 00 00'),
                    ])->columnSpanFull(),
            ]);
    }
}
