<?php

namespace App\Filament\Resources\Participants\Schemas;

use App\Enums\CategorieSP;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité du Participant')
                    ->description('Informations personnelles et identification légale')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
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
                    ]),

                Section::make('Rattachement Professionnel')
                    ->description('Entreprise cliente, poste occupé et statut CNSS')
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        Select::make('entreprise_id')
                            ->label('Entreprise Cliente')
                            ->relationship('entreprise', 'raison_sociale')
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('fonction_occupee')
                            ->label('Fonction occupée')
                            ->maxLength(255),

                        TextInput::make('numero_cnss')
                            ->label('Numéro CNSS')
                            ->numeric()
                            ->maxLength(20),

                        Select::make('categorie_sp')
                            ->label('Catégorie Socio-Professionnelle')
                            ->options(CategorieSP::class)
                            ->native(false)
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make('Coordonnées')
                    ->description('Moyens de contact du participant')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('email')
                            ->label('Adresse Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+212 6 00 00 00 00'),
                    ]),
            ]);
    }
}
