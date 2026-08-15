<?php

namespace App\Filament\Resources\EntrepriseClientes\RelationManagers;

use App\Exceptions\DocumentGenerationException;
use App\Models\EtudeIngenierieFormation;
use App\Services\GiacDocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Etude(s) d'Ingénierie de Formation d'une entreprise cliente : données
 * saisies en amont, nécessaires pour générer la fiche G4 (dossier GIAC).
 */
class EtudesIngenierieFormationRelationManager extends RelationManager
{
    protected static string $relationship = 'etudesIngenierieFormation';

    protected static ?string $title = "Études d'Ingénierie de Formation";

    protected static ?string $label = "Études d'Ingénierie de Formation";


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('formation_id')
                    ->label('Formation liée')
                    ->relationship(
                        name: 'formation',
                        titleAttribute: 'intitule',
                        modifyQueryUsing: fn ($query) => $query->where('entreprise_id', $this->getOwnerRecord()->id),
                    )
                    ->searchable()
                    ->preload()
                    ->unique(ignoreRecord: true)
                    ->helperText("Une formation ne peut avoir qu'une seule Étude d'Ingénierie de Formation.")
                    ->columnSpanFull(),

                TextInput::make('nature_action')
                    ->label("Nature de l'Action")
                    ->required()
                    ->maxLength(255)
                    ->default('Ingénierie de Formation')
                    ->columnSpanFull(),

                Textarea::make('diagnostic_besoins')
                    ->label('Diagnostic des Besoins en Formation')
                    ->rows(2)
                    ->columnSpanFull(),

                Grid::make(2)->schema([
                    Toggle::make('plan_formation')
                        ->label("Elaboration d'un Plan de Formation")
                        ->live(),
                    TextInput::make('plan_formation_annee')
                        ->label('Pour l\'année')
                        ->numeric()
                        ->minValue(2000)
                        ->visible(fn ($get) => (bool) $get('plan_formation')),
                ])->columnSpanFull(),

                Textarea::make('bilan_competence')
                    ->label('Bilan de Compétence')
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('autres_precisions')
                    ->label('Autres (à préciser)')
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('resultats_attendus')
                    ->label("Résultats attendus de l'Action")
                    ->rows(3)
                    ->columnSpanFull(),

                Grid::make(2)->schema([
                    DatePicker::make('periode_debut')
                        ->label('Période - Début')
                        ->displayFormat('d/m/Y')
                        ->native(false),

                    DatePicker::make('periode_fin')
                        ->label('Période - Fin')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->afterOrEqual('periode_debut'),
                ])->columnSpanFull(),

                Grid::make(2)->schema([
                    TextInput::make('nb_jours_intervention')
                        ->label("Nombre de jours d'Intervention")
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('cout_action')
                        ->label("Coût de l'Action (Hors Taxe, DH)")
                        ->numeric()
                        ->minValue(0),
                ])->columnSpanFull(),

                DatePicker::make('date_signature')
                    ->label('Date de Signature')
                    ->displayFormat('d/m/Y')
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nature_action')
            ->columns([
                TextColumn::make('formation.intitule')
                    ->label('Formation liée')
                    ->limit(30)
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nature_action')
                    ->label('Nature de l\'Action')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('periode_debut')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),

                /*TextColumn::make('periode_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),*/

                TextColumn::make('nb_jours_intervention')
                    ->label('Jours')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('cout_action')
                    ->label('Coût (DH)')
                    ->badge()
                    ->color('teal')
                    ->icon('heroicon-o-currency-dollar')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('date_signature')
                    ->label('Signée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('genererG4')
                    ->label('Générer G4')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function (EtudeIngenierieFormation $record, Action $action) {
                        try {
                            $document = app(GiacDocumentGenerationService::class)
                                ->generateFicheIngenierieFormation($this->getOwnerRecord(), $record);

                            return response()->streamDownload(
                                function () use ($document) {
                                    echo $document['content'];
                                },
                                $document['filename'],
                                ['Content-Type' => 'application/pdf']
                            );
                        } catch (DocumentGenerationException $e) {
                            Notification::make()
                                ->danger()
                                ->title('Génération impossible')
                                ->body($e->getMessage())
                                ->send();

                            $action->halt();
                        }
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
