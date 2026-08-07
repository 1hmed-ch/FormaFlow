<?php

namespace App\Filament\Resources\EntrepriseClientes\RelationManagers;

use App\Enums\CategorieSP;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';
    protected static ?string $label = 'Employé';
    protected static ?string $pluralLabel = 'Employés';

    protected static ?string $title = 'Employés';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->unique(table: 'participants', column: 'cin', ignoreRecord: true)
                    ->maxLength(20),

                /*Select::make('categorie_sp')
                    ->label('Catégorie Socio-Professionnelle')
                    ->options(CategorieSP::class)
                    ->native(false)
                    ->required(),*/

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
                    ->inline(),

                TextInput::make('fonction_occupee')
                    ->label('Fonction occupée')
                    ->maxLength(255),

                TextInput::make('numero_cnss')
                    ->label('Numéro CNSS')
                    ->numeric()
                    ->maxLength(20),

                TextInput::make('email')
                    ->label('Adresse Email')
                    ->email()
                    ->unique(table: 'participants', column: 'email', ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('telephone')
                    ->label('Téléphone')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('+212 6 00 00 00 00'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nom')
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable(),

                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable(),

                TextColumn::make('cin')
                    ->label('CIN')
                    ->searchable(),

                TextColumn::make('numero_cnss')
                    ->label('N° CNSS')
                    ->searchable(),

                TextColumn::make('categorie_sp')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'Ouvrier', 'O' => 'indigo',
                        'Cadre', 'C'   => 'warning',
                        'Employe', 'E' => 'success',
                        default        => 'gray',
                    }),

                TextColumn::make('fonction_occupee')
                    ->label('Fonction occupée')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categorie_sp')
                    ->label('Catégorie')
                    ->options(CategorieSP::class),
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
