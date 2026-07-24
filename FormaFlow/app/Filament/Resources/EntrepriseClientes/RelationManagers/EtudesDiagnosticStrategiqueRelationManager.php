<?php

namespace App\Filament\Resources\EntrepriseClientes\RelationManagers;

use App\Exceptions\DocumentGenerationException;
use App\Models\EtudeDiagnosticStrategique;
use App\Services\GiacDocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
 * Etude(s) de Diagnostic Stratégique d'une entreprise cliente : données
 * saisies en amont, nécessaires pour générer la fiche G6 (dossier GIAC).
 */
class EtudesDiagnosticStrategiqueRelationManager extends RelationManager
{
    protected static string $relationship = 'etudesDiagnosticStrategique';

    protected static ?string $title = 'Etudes de Diagnostic Stratégique';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Toggle::make('projet_marche_export')
                        ->label("Marché d'Exportation"),
                    Toggle::make('projet_investissement_techno')
                        ->label('Investissement Technologique'),
                    Toggle::make('projet_mise_aux_normes')
                        ->label('Mise aux Normes'),
                    Toggle::make('projet_autre')
                        ->label('Autre')
                        ->live(),
                ]),

                TextInput::make('projet_autre_precision')
                    ->label('Autre - à préciser')
                    ->maxLength(255)
                    ->visible(fn ($get) => (bool) $get('projet_autre'))
                    ->columnSpanFull(),

                Textarea::make('objectifs_resultats_attendus')
                    ->label('Objectifs et Résultats Attendus du Diagnostic')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('prestations_envisagees')
                    ->label("Prestations Envisagées (Proposition d'Intervention)")
                    ->rows(2)
                    ->columnSpanFull(),

                Grid::make(2)->schema([
                    TextInput::make('annee_application')
                        ->label("Année d'Application")
                        ->numeric()
                        ->minValue(2000)
                        ->required(),

                    TextInput::make('duree_intervention_jours')
                        ->label("Durée de l'Intervention (jours)")
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ]),

                Grid::make(2)->schema([
                    DatePicker::make('date_demarrage')
                        ->label('Date de Démarrage')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->required(),

                    TextInput::make('cout_previsionnel')
                        ->label("Coût Prévisionnel (Hors Taxe, DH)")
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ]),

                DatePicker::make('date_signature')
                    ->label('Date de Signature')
                    ->displayFormat('d/m/Y')
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('objectifs_resultats_attendus')
            ->columns([
                TextColumn::make('objectifs_resultats_attendus')
                    ->label('Objectifs')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('annee_application')
                    ->label('Année')
                    ->sortable(),

                TextColumn::make('duree_intervention_jours')
                    ->label('Jours')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('date_demarrage')
                    ->label('Démarrage')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('cout_previsionnel')
                    ->label('Coût (DH)')
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

                Action::make('genererG6')
                    ->label('Générer G6')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function (EtudeDiagnosticStrategique $record, Action $action) {
                        try {
                            $document = app(GiacDocumentGenerationService::class)
                                ->generateFicheDiagnosticStrategique($this->getOwnerRecord(), $record);

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
