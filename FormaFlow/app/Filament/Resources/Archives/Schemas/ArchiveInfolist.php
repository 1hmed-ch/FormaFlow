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
use Filament\Forms\Components\ToggleButtons;
use App\Enums\DemandeFinancementStatus;
use Filament\Forms\Components\TextInput as FormTextInput;
use App\Filament\Resources\Archives\Pages\ViewArchive;
use App\Enums\CategorieDocumentGenere;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
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
                            ->label('Entreprise :')
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
                                ->action(function (DossierGiac $record, ViewArchive $livewire, Action $action) {
                                    try {
                                        $zipPath = app(ArchiveZipService::class)->build(
                                            $record,
                                            $livewire->archiveDocumentsCategorie,
                                            $livewire->archiveDocumentsDateDebut,
                                            $livewire->archiveDocumentsDateFin
                                        );

                                        return response()->download($zipPath)->deleteFileAfterSend(true);
                                    } catch (\RuntimeException $e) {
                                        Notification::make()
                                            ->warning()
                                            ->title('Archive vide')
                                            ->body($e->getMessage())
                                            ->send();

                                        $action->halt();
                                    }
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

                // Bloc 2 : GIAC Technologies & Checklist Signée
                Section::make('Documents du cabinet et Checklist signée')
                    ->description('Documents générés par le cabinet et pièces signées')
                    ->icon('heroicon-o-building-office')
                    ->columnSpanFull()
                    ->collapsible()
                    ->headerActions([
                        Action::make('filtrerDocumentsGeneresArchive')
                            ->label('Filtrer')
                            ->icon('heroicon-o-funnel')
                            ->color('gray')
                            ->fillForm(fn (ViewArchive $livewire): array => [
                                'categorie' => $livewire->archiveDocumentsCategorie,
                                'genere_du' => $livewire->archiveDocumentsDateDebut,
                                'genere_au' => $livewire->archiveDocumentsDateFin,
                            ])
                            ->form([
                                /*Select::make('categorie')
                                    ->label('Catégorie')
                                    ->options(CategorieDocumentGenere::class)
                                    ->native(false)
                                    ->placeholder('Toutes les catégories'),*/
                                ToggleButtons::make('categorie')
                                    ->label('Catégorie')
                                    ->options(CategorieDocumentGenere::class)
                                    ->colors([
                                        CategorieDocumentGenere::Remboursement->value => 'indigo',
                                        CategorieDocumentGenere::Giac->value => 'orange',
                                        CategorieDocumentGenere::Ofppt->value => 'success',
                                        CategorieDocumentGenere::Entreprise->value => 'yellow',
                                    ])
                                    ->icons([
                                        CategorieDocumentGenere::Remboursement->value => 'heroicon-o-document-text',
                                        CategorieDocumentGenere::Giac->value => 'heroicon-o-document-check',
                                        CategorieDocumentGenere::Ofppt->value => 'heroicon-o-document-magnifying-glass',
                                        CategorieDocumentGenere::Entreprise->value => 'heroicon-o-document-plus',
                                    ])
                                    ->inline(),

                                DatePicker::make('genere_du')
                                    ->label('Généré à partir du')
                                    ->native(false),

                                DatePicker::make('genere_au')
                                    ->label("Généré jusqu'au")
                                    ->native(false)
                                    ->afterOrEqual('genere_du'),
                            ])
                            ->action(function (array $data, ViewArchive $livewire) {
                                $categorie = $data['categorie'] ?? null;

                                $livewire->archiveDocumentsCategorie = $categorie instanceof CategorieDocumentGenere
                                    ? $categorie->value
                                    : $categorie;

                                $livewire->archiveDocumentsDateDebut = $data['genere_du'] ?? null;
                                $livewire->archiveDocumentsDateFin = $data['genere_au'] ?? null;
                            }),

                        Action::make('reinitialiserFiltreDocumentsGeneresArchive')
                            ->label('Réinitialiser')
                            ->icon('heroicon-o-x-mark')
                            ->color('gray')
                            ->visible(fn (ViewArchive $livewire): bool => filled($livewire->archiveDocumentsCategorie)
                                || filled($livewire->archiveDocumentsDateDebut)
                                || filled($livewire->archiveDocumentsDateFin))
                            ->action(function (ViewArchive $livewire) {
                                $livewire->archiveDocumentsCategorie = null;
                                $livewire->archiveDocumentsDateDebut = null;
                                $livewire->archiveDocumentsDateFin = null;
                            }),
                    ])
                    ->schema([
                        RepeatableEntry::make('entrepriseCliente.documentsGeneres')
                            ->hiddenLabel()
                            ->state(function (DossierGiac $record, ViewArchive $livewire) {
                                $entreprise = $record->entrepriseCliente;
                                if (! $entreprise) {
                                    return collect();
                                }

                                $query = $entreprise->documentsGeneres();

                                if (filled($livewire->archiveDocumentsCategorie)) {
                                    $query->where('categorie', $livewire->archiveDocumentsCategorie);
                                }
                                if (filled($livewire->archiveDocumentsDateDebut)) {
                                    $query->whereDate('genere_le', '>=', $livewire->archiveDocumentsDateDebut);
                                }
                                if (filled($livewire->archiveDocumentsDateFin)) {
                                    $query->whereDate('genere_le', '<=', $livewire->archiveDocumentsDateFin);
                                }

                                return $query->latest('genere_le')->get();
                            })
                            ->table([
                                TableColumn::make('Type'),
                                TableColumn::make('Catégorie'),
                                TableColumn::make('Version'),
                                TableColumn::make('Statut'),
                                TableColumn::make('Généré le'),
                                /*TableColumn::make('Taille'),*/
                                TableColumn::make('')->hiddenHeaderLabel(),
                                TableColumn::make('')->hiddenHeaderLabel(),
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
                                /*TextEntry::make('taille')
                                    ->formatStateUsing(fn ($record): string => $record->tailleLisible())
                                    ->color('teal')
                                    ->badge(),*/


                                // Bouton "Télécharger"
                                TextEntry::make('nom_fichier')
                                    ->label('')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('primary')
                                    ->tooltip('Télécharger le Document')
                                    ->url(fn ($record): string => route('documents-generes.telecharger', $record))
                                    ->openUrlInNewTab(),
                                 // Bouton "Voir"
                                Actions::make([
                                    Action::make('voirDocumentGenere')
                                        ->label('')
                                        ->icon('heroicon-o-eye')
                                        ->color('info')
                                        ->size('sm')
                                        ->tooltip('Aperçu')
                                        ->modalHeading(fn ($record) => $record->nom_fichier)
                                        ->modalContent(fn ($record) => view('filament.modals.apercu-fichier', [
                                            'url' => route('documents-generes.stream', $record),
                                            'mime' => 'application/pdf',
                                        ]))
                                        ->modalSubmitAction(false)
                                        ->modalCancelAction(false)
                                        ->modalWidth('4xl'),
                                ]),
                                // Bouton "Supprimer"
                                Actions::make([
                                    Action::make('supprimerDocumentArchive')
                                        ->label('')
                                        ->icon('heroicon-o-trash')
                                        ->color('danger')
                                        ->size('sm')
                                        ->tooltip('supprimer')
                                        ->requiresConfirmation()
                                        ->modalHeading('Supprimer le document')
                                        ->modalDescription('Cette action est irréversible.')
                                        ->action(function ($record, Action $action) {
                                            try {
                                                $record->delete();
                                                Notification::make()->success()->title('Document supprimé')->send();
                                            } catch (\Exception $e) {
                                                Notification::make()->danger()->title('Erreur lors de la suppression')->send();
                                                $action->halt();
                                            }
                                        }),
                                ]),
                            ])
                            ->placeholder(fn (ViewArchive $livewire): string => (
                                filled($livewire->archiveDocumentsCategorie)
                                || filled($livewire->archiveDocumentsDateDebut)
                                || filled($livewire->archiveDocumentsDateFin)
                            )
                                ? 'Aucun document ne correspond aux critères sélectionnés.'
                                : 'Aucun document généré pour le moment.'),

                        // Sous-section 1 : Documents signés de l'entreprise
                        Section::make('Documents signés (Entreprise)')
                            ->description('Pièces de la checklist signées par l\'entreprise')
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                                    ->schema(self::checklistGiacCards())
                                    ->columnSpanFull(),
                            ])
                            ->compact(),

                        // Sous-section 2 : Documents signés de l'organisme de formation
                        Section::make('Documents signés (Organisme de formation)')
                            ->description('Pièces signées du cabinet')
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                                    ->schema(self::piecesJointesCabinetCards())
                                    ->columnSpanFull(),
                            ])
                            ->compact(),
                    ]),

                // Bloc 3 : Documents à joindre
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
                            // Bouton Supprimer 
                                TextEntry::make('supprimer')
                                    ->label('')
                                    ->state('Supprimer')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->action(
                                        Action::make('supprimerAutreDoc')
                                            ->color('danger')
                                            ->requiresConfirmation()
                                            ->modalHeading('Supprimer le document')
                                            ->modalDescription('Cette action est irréversible.')
                                            ->action(function ($record, $livewire) {
                                                $record->delete();
                                                Notification::make()->success()->title('Document supprimé')->send();
                                                $livewire->dispatch('$refresh');
                                            })
                                    ),
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

                // Bloc 4 : Demande de Financement (OFPPT)
                Section::make('Demande de Financement (OFPPT)')
                    ->description('Suivi du dossier de financement OFPPT')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('entrepriseCliente.statut_demande_financement')
                            ->label('Statut de la demande')
                            ->badge()
                            ->formatStateUsing(fn (DemandeFinancementStatus $state) => $state->getLabel())
                            ->color(fn (DemandeFinancementStatus $state) => match ($state) {
                                DemandeFinancementStatus::EN_COURS => 'warning',
                                DemandeFinancementStatus::ACCEPTEE => 'success',
                                DemandeFinancementStatus::REFUSEE => 'danger',
                                DemandeFinancementStatus::ARCHIVEE => 'gray',
                            }),

                        Actions::make([
                            Action::make('modifierStatutDemandeFinancement')
                                ->label('Modifier le statut')
                                ->icon('heroicon-o-pencil-square')
                                ->color('gray')
                                ->form([
                                    ToggleButtons::make('statut_demande_financement')
                                        ->label('Statut de la demande')
                                        ->options(DemandeFinancementStatus::class)
                                        ->colors([
                                            DemandeFinancementStatus::EN_COURS->value => 'warning',
                                            DemandeFinancementStatus::ACCEPTEE->value => 'success',
                                            DemandeFinancementStatus::REFUSEE->value => 'danger',
                                            DemandeFinancementStatus::ARCHIVEE->value => 'gray',
                                        ])
                                        ->icons([
                                            DemandeFinancementStatus::EN_COURS->value => 'heroicon-o-clock',
                                            DemandeFinancementStatus::ACCEPTEE->value => 'heroicon-o-check-circle',
                                            DemandeFinancementStatus::REFUSEE->value => 'heroicon-o-x-circle',
                                            DemandeFinancementStatus::ARCHIVEE->value => 'heroicon-o-archive-box',
                                        ])
                                        ->inline()
                                        ->required(),
                                ])
                                ->fillForm(fn (DossierGiac $record) => [
                                    'statut_demande_financement' => $record->entrepriseCliente?->statut_demande_financement,
                                ])
                                ->action(function (DossierGiac $record, array $data) {
                                    $entreprise = $record->entrepriseCliente;
                                    if (! $entreprise) {
                                        return;
                                    }

                                    $entreprise->update([
                                        'statut_demande_financement' => $data['statut_demande_financement'],
                                    ]);

                                    Notification::make()->success()->title('Statut mis à jour avec succès')->send();
                                }),
                        ])->columnSpanFull()->alignment('end'),

                        Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                            ->schema(self::piecesJointesOfpptCards())
                            ->columnSpanFull(),

                        RepeatableEntry::make('autres_documents_ofppt_display')
                            ->label('Autres documents de financement')
                            ->state(fn (DossierGiac $record) => $record->entrepriseCliente?->getMedia('autres_documents_ofppt') ?? collect())
                            ->columnSpanFull()
                            ->table([
                                TableColumn::make('Intitulé'),
                                TableColumn::make('Ajouté le'),
                                TableColumn::make('')->hiddenHeaderLabel(),
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
                                        Action::make('voirAutreDocOfppt')
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
                            TextEntry::make('supprimer')
                                    ->label('')
                                    ->state('Supprimer')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->action(
                                        Action::make('supprimerAutreDocOfppt')
                                            ->color('danger')
                                            ->requiresConfirmation()
                                            ->modalHeading('Supprimer le document')
                                            ->modalDescription('Cette action est irréversible.')
                                            ->action(function ($record, $livewire) {
                                                $record->delete();
                                                Notification::make()->success()->title('Document supprimé')->send();
                                                $livewire->dispatch('$refresh');
                                            })
                                    ),
                            ])
                            ->placeholder('Aucun document de financement complémentaire.'),

                        Actions::make([
                            Action::make('modalAjouterAutreDocOfppt')
                                ->label('Ajouter un document de financement')
                                ->icon('heroicon-o-document-plus')
                                ->color('primary')
                                ->form([
                                    FormTextInput::make('nouvel_intitule')
                                        ->label('Intitulé du document')
                                        ->required(),
                                    FileUpload::make('upload_autre_doc_ofppt')
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
                                        ->addMediaFromDisk($data['upload_autre_doc_ofppt'], 'local')
                                        ->withCustomProperties(['intitule' => $data['nouvel_intitule']])
                                        ->toMediaCollection('autres_documents_ofppt');

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

    protected static function checklistGiacCards(): array
    {
        $cards = [];

        foreach (\App\Models\DossierGiac::PIECES_JOINTES as $key => $label) {
            $getStatut = fn (DossierGiac $record) => $record->entrepriseCliente
                ? \App\Models\DossierGiac::pourEntreprise($record->entrepriseCliente)->getPieceJointeStatut($key)
                : ['etat' => 'Manquant', 'media' => null, 'nom_fichier' => null, 'date_ajout' => null];

            $cards[] = Section::make($label)
                ->icon(fn (DossierGiac $record) => $getStatut($record)['media'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (DossierGiac $record) => $getStatut($record)['media'] ? 'success' : 'warning')
                ->description(fn (DossierGiac $record) => $getStatut($record)['media'] ? 'Déposé' : 'Manquant')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('date_ajout_checklist_' . $key)
                        ->label("Date d'ajout")
                        ->state(fn (DossierGiac $record) => $getStatut($record)['date_ajout']?->format('d/m/Y') ?? '—'),

                    Actions::make([
                        Action::make('voir_checklist_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible(fn (DossierGiac $record) => (bool) $getStatut($record)['media'])
                            ->modalHeading($label)
                            ->modalContent(function (DossierGiac $record) use ($key, $getStatut) {
                                $media = $getStatut($record)['media'];
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

                        Action::make('telecharger_checklist_' . $key)
                            ->label('Télécharger')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->visible(fn (DossierGiac $record) => (bool) $getStatut($record)['media'])
                            ->action(function (DossierGiac $record) use ($getStatut) {
                                $media = $getStatut($record)['media'];
                                if (! $media || ! file_exists($media->getPath())) {
                                    Notification::make()->danger()->title('Fichier introuvable')->send();
                                    return;
                                }

                                return response()->download($media->getPath(), $media->file_name);
                            }),

                        Action::make('gerer_checklist_' . $key)
                            ->label(fn (DossierGiac $record) => $getStatut($record)['media'] ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color(fn (DossierGiac $record) => $getStatut($record)['media'] ? 'gray' : 'primary')
                            ->modalHeading('Gestion de la pièce : ' . $label)
                            ->form([
                                FileUpload::make('document')
                                    ->label('Document (PDF / Image)')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->maxSize(10240)
                                    ->disk('local')
                                    ->directory('giac-checklist-tmp')
                                    ->required(),
                            ])
                            ->action(function (array $data, DossierGiac $record, $livewire) use ($key, $label) {
                                $entreprise = $record->entrepriseCliente;
                                if (! $entreprise) {
                                    return;
                                }

                                \App\Models\DossierGiac::pourEntreprise($entreprise)
                                    ->addMediaFromDisk($data['document'], 'local')
                                    ->toMediaCollection($key);

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),
                   Action::make('supprimer_checklist_' . $key)
                            ->label('Supprimer')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible(fn (DossierGiac $record) => (bool) $getStatut($record)['media'])
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function (DossierGiac $record, $livewire) use ($getStatut) {
                                $media = $getStatut($record)['media'];
                                if ($media) {
                                    $media->delete();
                                    Notification::make()->success()->title('Document supprimé avec succès')->send();
                                    $livewire->dispatch('$refresh');
                                }
                            }),
                    ]),
                
                ]);
        }

        return $cards;
    }

    protected static function piecesJointesCabinetCards(): array
    {
        $cards = [];

        $piecesAAfficher = [
            'fiche_identification' => 'Fiche d’identification de l’organisme de formation',
            'fiche_renseignement'  => 'Fiche de renseignement de l’organisme de conseil',
        ];

        foreach ($piecesAAfficher as $key => $label) {
            $organisme = \App\Models\EntrepriseFormation::current();
            $statut = $organisme->getPieceJointeStatut($key);

            $cards[] = Section::make($label)
                ->description('Pièce du cabinet')
                ->icon($statut['media'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor($statut['media'] ? 'success' : 'warning')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('cabinet_etat_' . $key)
                        ->label('État')
                        ->state($statut['media'] ? 'Déposé' : 'Manquant')
                        ->badge()
                        ->color($statut['media'] ? 'success' : 'danger'),

                    TextEntry::make('cabinet_date_ajout_' . $key)
                        ->label("Date d'ajout")
                        ->state($statut['date_ajout']?->format('d/m/Y') ?? '—'),

                    Actions::make([
                        Action::make('voir_cabinet_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible((bool) $statut['media'])
                            ->modalHeading($label)
                            ->modalContent(function () use ($key) {
                                $media = \App\Models\EntrepriseFormation::current()->getFirstMedia($key);
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

                        Action::make('telecharger_cabinet_' . $key)
                            ->label('Télécharger')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->visible((bool) $statut['media'])
                            ->action(function () use ($key) {
                                $media = \App\Models\EntrepriseFormation::current()->getFirstMedia($key);
                                if (! $media || ! file_exists($media->getPath())) {
                                    Notification::make()->danger()->title('Fichier introuvable')->send();
                                    return;
                                }

                                return response()->download($media->getPath(), $media->file_name);
                            }),

                        Action::make('gerer_cabinet_' . $key)
                            ->label($statut['media'] ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color($statut['media'] ? 'gray' : 'primary')
                            ->modalHeading('Gestion de la pièce : ' . $label)
                            ->form([
                                FileUpload::make('document')
                                    ->label('Document (PDF / Image)')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->maxSize(10240)
                                    ->disk('local')
                                    ->directory('cabinet-checklist-tmp')
                                    ->required(),
                            ])
                            ->action(function (array $data, $livewire) use ($key, $label) {
                                \App\Models\EntrepriseFormation::current()
                                    ->addMediaFromDisk($data['document'], 'local')
                                    ->toMediaCollection($key);

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),
                   Action::make('supprimer_cabinet_' . $key)
                            ->label('Supprimer')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible((bool) $statut['media'])
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function ($livewire) use ($key) {
                                $media = \App\Models\EntrepriseFormation::current()->getFirstMedia($key);
                                if ($media) {
                                    $media->delete();
                                    Notification::make()->success()->title('Document supprimé avec succès')->send();
                                    $livewire->dispatch('$refresh');
                                }
                            }),
                    ]),
                ]);
        }

        return $cards;
    }

    protected static function piecesJointesOfpptCards(): array
    {
        $cards = [];

        foreach (EntrepriseCliente::PIECES_JOINTES_OFPPT as $key => $label) {
            $hasMedia = fn (DossierGiac $record) => $record->entrepriseCliente?->hasMedia($key);

            $cards[] = Section::make($label)
                ->icon(fn (DossierGiac $record) => $hasMedia($record) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (DossierGiac $record) => $hasMedia($record) ? 'success' : 'warning')
                ->description(fn (DossierGiac $record) => $hasMedia($record) ? 'Déposé' : 'Manquant')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('date_ajout_' . $key)
                        ->label("Date d'ajout")
                        ->state(fn (DossierGiac $record) => $record->entrepriseCliente?->getFirstMedia($key)?->created_at)
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),

                    Actions::make([
                        Action::make('voir_ofppt_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible($hasMedia)
                            ->modalHeading($label)
                            ->modalContent(function (DossierGiac $record) use ($key) {
                                $media = $record->entrepriseCliente?->getFirstMedia($key);
                                if (! $media) return null;

                                return view('filament.modals.apercu-fichier', [
                                    'url' => route('media.stream', $media),
                                    'mime' => $media->mime_type,
                                ]);
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalWidth('4xl'),

                        Action::make('telecharger_ofppt_' . $key)
                            ->label('Télécharger')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->visible($hasMedia)
                            ->url(fn (DossierGiac $record) => route('media.stream', $record->entrepriseCliente?->getFirstMedia($key)))
                            ->openUrlInNewTab(),

                        Action::make('gerer_ofppt_' . $key)
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
                                if (! $entreprise) return;

                                $entreprise
                                    ->addMediaFromDisk($data['fichier_' . $key], 'local')
                                    ->toMediaCollection($key);

                                $entreprise->unsetRelation('media');
                                $record->unsetRelation('entrepriseCliente');

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),
                 Action::make('supprimer_ofppt_' . $key)
                            ->label('Supprimer')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible($hasMedia)
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function (DossierGiac $record, $livewire) use ($key) {
                                $media = $record->entrepriseCliente?->getFirstMedia($key);
                                if ($media) {
                                    $media->delete();
                                    $record->entrepriseCliente->unsetRelation('media');
                                    $record->unsetRelation('entrepriseCliente');
                                    Notification::make()->success()->title('Document supprimé avec succès')->send();
                                    $livewire->dispatch('$refresh');
                                }
                            }),
                    ]),
                ]);
        }

        return $cards;
    }

    protected static function piecesJointesCards(): array
    {
        $cards = [];
        $pieces = collect(EntrepriseCliente::PIECES_JOINTES)->except([
            'entete_page', 
            'pied_page',
        ]);

        foreach ($pieces as $key => $label) {
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

                        Action::make('telecharger_' . $key)
                            ->label('Télécharger')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->visible($hasMedia)
                            ->url(fn (DossierGiac $record) => route('media.stream', $record->entrepriseCliente?->getFirstMedia($key)))
                            ->openUrlInNewTab(),

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
                   Action::make('supprimer_' . $key)
                            ->label('Supprimer')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible($hasMedia)
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function (DossierGiac $record, $livewire) use ($key) {
                                $media = $record->entrepriseCliente?->getFirstMedia($key);
                                if ($media) {
                                    $media->delete();
                                    $record->entrepriseCliente->unsetRelation('media');
                                    $record->unsetRelation('entrepriseCliente');
                                    Notification::make()->success()->title('Document supprimé avec succès')->send();
                                    $livewire->dispatch('$refresh');
                                }
                            }),
                    ]),
                ]);
        }

        return $cards;
    }
}
