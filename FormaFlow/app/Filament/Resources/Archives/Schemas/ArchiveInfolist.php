<?php

namespace App\Filament\Resources\Archives\Schemas;

use App\Exceptions\DocumentGenerationException;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use App\Services\DocumentGenerationService;
use App\Services\ArchiveZipService;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput as FormTextInput;

class ArchiveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // Bloc 1 : Fiche entreprise
                Section::make('Fiche entreprise')
                    ->description('Fiche d\'information générale de l\'entreprise')
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('entrepriseCliente.raison_sociale')
                            ->label('Entreprise')
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),

                    
                        Actions::make([
                            Action::make('genererFicheEntreprise')
                                ->label('Générer la fiche')
                                ->icon('heroicon-o-document-arrow-down')
                                ->color('primary')
                                ->action(function (DossierGiac $record, Action $action) {
                                    try {
                                        $document = app(DocumentGenerationService::class)
                                            ->generateFicheAccesClient($record->entrepriseCliente);

                                        return response()->streamDownload(
                                            fn () => print($document['content']),
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

                            Action::make('telechargerZip')
                                ->label('Dossier complet (ZIP)')
                                ->icon('heroicon-o-archive-box-arrow-down')
                                ->color('success')
                                ->action(function (DossierGiac $record) {
                                    $zipPath = app(ArchiveZipService::class)->build($record);

                                    return response()->download($zipPath)->deleteFileAfterSend(true);
                                }),

                            Action::make('imprimerDossier')
                                ->label('Imprimer')
                                ->icon('heroicon-o-printer')
                                ->color('gray')
                                ->action(function (DossierGiac $record) {
                                    $document = app(DocumentGenerationService::class)
                                        ->generateImpressionDossier($record);

                                    return response()->streamDownload(
                                        fn () => print($document['content']),
                                        $document['filename'],
                                        ['Content-Type' => 'application/pdf']
                                    );
                                }),
                        ])
                            ->columnSpanFull()
                            ->alignment('end'),
                    ]),

                //Bloc 2 : GIAC Technologies
                Section::make('GIAC Technologies')
                    ->description('Documents générés par le cabinet pour ce dossier (lecture seule)')
                    ->icon('heroicon-o-building-office')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('checklist_statut')
                            ->label('Statut checklist entreprise')
                            ->state('Non disponible')
                            ->badge()
                            ->color('gray'),

                        RepeatableEntry::make('entrepriseCliente.documentsGeneres')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Type'),
                                TableColumn::make('Catégorie'),
                                TableColumn::make('Version'),
                                TableColumn::make('Statut'),
                                TableColumn::make('Généré le'),
                                TableColumn::make('Taille'),
                                TableColumn::make('')->hiddenHeaderLabel(),
                            ])
                            ->schema([
                                TextEntry::make('type_document'),
                                TextEntry::make('categorie')->badge(),
                                TextEntry::make('version')
                                    ->formatStateUsing(fn (int $state): string => "v{$state}")
                                    ->color('indigo')
                                    ->badge(),
                                TextEntry::make('statut')->badge(),
                                TextEntry::make('genere_le')->dateTime('d/m/Y H:i'),
                                TextEntry::make('taille')
                                    ->formatStateUsing(fn ($record): string => $record->tailleLisible())
                                    ->color('teal')
                                    ->badge(),
                                TextEntry::make('nom_fichier')
                                    ->label('')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('primary')
                                    ->url(fn ($record): string => route('documents-generes.telecharger', $record))
                                    ->openUrlInNewTab(),
                            ])
                            ->placeholder('Aucun document GIAC généré pour cette entreprise.'),
                    ]),

                //Bloc 3 : Documents à joindre
                Section::make('Documents à joindre')
                    ->description('Pièces fournies par l\'entreprise')
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([

                        Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                            ->schema(self::piecesJointesCards())
                            ->columnSpanFull(),

                        RepeatableEntry::make('autres_documents_display')
                            ->label('Autres documents existants')
                            ->state(fn (DossierGiac $record) => $record->entrepriseCliente?->getMedia('autres_documents') ?? collect())
                            ->columnSpanFull()
                            ->table([
                                TableColumn::make('Intitulé'),
                                TableColumn::make('Ajouté le'),
                                TableColumn::make('')->hiddenHeaderLabel(),
                                TableColumn::make('')->hiddenHeaderLabel(),
                            ])
                            ->schema([
                                TextEntry::make('intitule')
                                    ->state(fn ($record) => $record->getCustomProperty('intitule') ?: $record->file_name),
                                TextEntry::make('created_at')
                                    ->dateTime('d/m/Y H:i'),

                                TextEntry::make('voir')
                                    ->label('')
                                    ->state('Voir')
                                    ->icon('heroicon-o-eye')
                                    ->color('info')
                                    ->action(
                                        Action::make('voirAutreDocument')
                                            ->modalHeading(fn ($record) => $record->getCustomProperty('intitule') ?: $record->file_name)
                                            ->modalContent(fn ($record) => view('filament.modals.apercu-fichier', [
                                                'url' => route('media.stream', $record),
                                                'mime' => $record->mime_type,
                                            ]))
                                            ->modalSubmitAction(false)
                                            ->modalCancelAction(false)
                                            ->modalWidth('4xl')
                                    ),

                                TextEntry::make('telecharger')
                                    ->label('')
                                    ->state('Télécharger')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('primary')
                                    ->url(fn ($record) => route('media.stream', $record))
                                    ->openUrlInNewTab(),
                            ])
                            ->placeholder('Aucun document complémentaire ajouté.'),

                        Actions::make([
                            Action::make('modalAjouterAutreDocument')
                                ->label('Ajouter un autre document')
                                ->icon('heroicon-o-document-plus')
                                ->color('primary')
                                ->form([
                                    FormTextInput::make('nouvel_intitule')
                                        ->label('Intitulé du document')
                                        ->required(),
                                    FileUpload::make('upload_autre_document')
                                        ->label('Fichier')
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(10240)
                                        ->disk('local')
                                        ->directory('tmp-archives-upload')
                                        ->visibility('private')
                                        ->required(),
                                ])
                                ->action(function (DossierGiac $record, array $data, $livewire) {
                                    $entreprise = $record->entrepriseCliente;
                                    if (! $entreprise) {
                                        return;
                                    }

                                    $entreprise
                                        ->addMediaFromDisk($data['upload_autre_document'], 'local')
                                        ->withCustomProperties(['intitule' => $data['nouvel_intitule']])
                                        ->toMediaCollection('autres_documents');

                                    
                                    $entreprise->unsetRelation('media');
                                    $record->unsetRelation('entrepriseCliente');

                                    Notification::make()->success()->title('Document ajouté avec succès')->send();
                                    $livewire->dispatch('$refresh');
                                }),
                        ])
                            ->columnSpanFull()
                            ->alignment('end'),
                    ]),
            ]);
    }

    
    protected static function piecesJointesCards(): array
    {
        $cards = [];

        foreach (EntrepriseCliente::PIECES_JOINTES as $key => $label) {
            $hasMedia = fn (DossierGiac $record) => $record->entrepriseCliente?->hasMedia($key);

            $cards[] = Section::make($label)
                ->icon(fn (DossierGiac $record) => $hasMedia($record) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (DossierGiac $record) => $hasMedia($record) ? 'success' : 'warning')
                ->description(fn (DossierGiac $record) => $record->entrepriseCliente?->getPieceJointeStatut($key) ?? 'Manquant')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    Actions::make([
                        Action::make('voir_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible($hasMedia)
                            ->modalHeading($label)
                            ->modalContent(function (DossierGiac $record) use ($key) {
                                $media = $record->entrepriseCliente?->getFirstMedia($key);

                                if (! $media) {
                                    return null;
                                }

                                return view('filament.modals.apercu-fichier', [
                                    'url' => route('media.stream', $media),
                                    'mime' => $media->mime_type,
                                ]);
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalWidth('4xl'),

                        Action::make('gerer_' . $key)
                            ->label(fn (DossierGiac $record) => $hasMedia($record) ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color(fn (DossierGiac $record) => $hasMedia($record) ? 'gray' : 'primary')
                            ->modalHeading("Gestion de la pièce : " . $label)
                            ->form([
                                FileUpload::make('fichier_' . $key)
                                    ->label('Téléverser le fichier (PDF / Image)')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->maxSize(10240)
                                    ->disk('local')
                                    ->directory('tmp-archives-upload')
                                    ->visibility('private')
                                    ->required(),
                            ])
                            ->action(function (DossierGiac $record, array $data, $livewire) use ($key, $label) {
                                $entreprise = $record->entrepriseCliente;
                                if (! $entreprise) {
                                    return;
                                }

                                $entreprise
                                    ->addMediaFromDisk($data['fichier_' . $key], 'local')
                                    ->toMediaCollection($key);

                                $entreprise->unsetRelation('media');
                                $record->unsetRelation('entrepriseCliente');

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),
                    ]),
                ]);
        }

        return $cards;
    }
}