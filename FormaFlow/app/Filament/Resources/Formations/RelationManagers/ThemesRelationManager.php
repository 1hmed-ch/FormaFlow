<?php

namespace App\Filament\Resources\Formations\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class ThemesRelationManager extends RelationManager
{
    protected static string $relationship = 'themes';

    protected static ?string $title = 'Thèmes de la Formation';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('intitule')
                    ->label('Intitulé du thème')
                    ->placeholder('Ex: Architecture Microservices, Management Agile...')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('formateur_id')
                    ->label('Formateur assigné')
                    ->relationship('formateur','nom')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['nom', 'prenom'])
                    ->preload()
                    ->required(),
                Textarea::make('objectifs')
                    ->label('Objectifs pédagogiques')
                    ->placeholder('Décrivez les compétences visées par ce thème...')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('intitule')
            ->columns([
                TextColumn::make('intitule')
                    ->label('Thème')
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
                TextColumn::make('formateur.full_name')
                    ->label('Formateur')
                    ->searchable(['nom', 'prenom'])
                    ->sortable(['nom', 'prenom']),
            ])
            ->filters([
                SelectFilter::make('formateur_id')
                    ->label('Formateur')
                    ->relationship('formateur', 'nom')
                    ->searchable()
                    ->preload(),
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
