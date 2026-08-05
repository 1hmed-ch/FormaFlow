<?php

namespace App\Filament\Resources\Formateurs\Schemas;

use App\Enums\FormateurStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormateurForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité & Spécialité')
                    ->description('Informations personnelles et domaine d\'expertise du formateur')
                    ->icon('heroicon-o-user')
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

                        TextInput::make('specialite')
                            ->label('Spécialité')
                            ->placeholder('Ex: Développement Web, Management...')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        /*Select::make('statut')
                            ->label('Statut du formateur')
                            ->options(FormateurStatus::class)
                            ->default('INTERNE')
                            ->native(false)
                            ->required()
                            ->columnSpanFull(),*/
                        ToggleButtons::make('statut')
                            ->label('Statut du formateur')
                            ->options(FormateurStatus::class)
                            ->default(FormateurStatus::INTERNE->value)
                            ->colors([
                                FormateurStatus::INTERNE->value => 'success',
                                FormateurStatus::EXTERNE->value => 'warning',
                            ])
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->label('Adresse Email')
                            ->email()
                            ->placeholder("saisissez l'adresse email")
                            ->unique(ignoreRecord: true)
                            ->required()
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
