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
use Filament\Forms\Components\TextInput as FormTextInput;
use App\Filament\Resources\Archives\Pages\ViewArchive;
use App\Enums\CategorieDocumentGenere;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use App\Filament\Concerns\HasPiecesJointesCards;
class ArchiveInfolist
{
    use HasPiecesJointesCards;

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

                // Bloc 2 : GIAC Technologies 
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
                                'formation_id' => $livewire->archiveDocumentsFormationId,
                                'genere_du' => $livewire->archiveDocumentsDateDebut,
                                'genere_au' => $livewire->archiveDocumentsDateFin,
                            ])
                            ->form([
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

                                Select::make('formation_id')
                                    ->label('Formation (documents "Remboursement")')
                                    ->native(false)
                                    ->searchable()
                                    ->options(fn (DossierGiac $record) => $record->entrepriseCliente
                                        ?->formations()
                                        ->orderByDesc('date_debut')
                                        ->pluck('intitule', 'id') ?? [])
                                    ->placeholder('Toutes les formations'),

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

                                $livewire->archiveDocumentsFormationId = $data['formation_id'] ?? null;
                                $livewire->archiveDocumentsDateDebut = $data['genere_du'] ?? null;
                                $livewire->archiveDocumentsDateFin = $data['genere_au'] ?? null;
                            }),

                        Action::make('reinitialiserFiltreDocumentsGeneresArchive')
                            ->label('Réinitialiser')
                            ->icon('heroicon-o-x-mark')
                            ->color('gray')
                            ->visible(fn (ViewArchive $livewire): bool => filled($livewire->archiveDocumentsCategorie)
                                || filled($livewire->archiveDocumentsFormationId)
                                || filled($livewire->archiveDocumentsDateDebut)
                                || filled($livewire->archiveDocumentsDateFin))
                            ->action(function (ViewArchive $livewire) {
                                $livewire->archiveDocumentsCategorie = null;
                                $livewire->archiveDocumentsFormationId = null;
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

                                $documents = $query->latest('genere_le')->get();

                                if (filled($livewire->archiveDocumentsFormationId)) {
                                    $documents = $documents
                                        ->filter(fn ($document) => $document->formationLiee()?->getKey() === (int) $livewire->archiveDocumentsFormationId)
                                        ->values();
                                }

                                return $documents;
                            })
                            ->table([
                                TableColumn::make('Type'),
                                TableColumn::make('Catégorie'),
                                TableColumn::make('Formation'),
                                TableColumn::make('Version'),
                                //TableColumn::make('Statut'),
                                TableColumn::make('Généré le'),
                                /*TableColumn::make('Taille'),*/
                                TableColumn::make('')->hiddenHeaderLabel(),
                                TableColumn::make('')->hiddenHeaderLabel(),
                                TableColumn::make('')->hiddenHeaderLabel(),
                            ])
                            ->schema([
                                TextEntry::make('type_document'),
                                TextEntry::make('categorie')->badge(),
                                TextEntry::make('formation_liee')
                                    ->state(fn ($record) => $record->formationLiee()?->intitule ?? '—')
                                    ->color('gray'),
                                TextEntry::make('version')
                                    ->formatStateUsing(fn (int $state): string => "v{$state}")
                                    ->color('indigo')
                                    ->badge(),
                                //TextEntry::make('statut')->badge(),
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

                    ]),

                // Bloc 3 : Documents à joindre
                Section::make('Documents à joindre')
                    ->description('Pièces fournies par l\'entreprise')
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                            ->schema(self::piecesAJoindreEntrepriseCards())
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

            ]);
    }
}
