<?php

namespace App\Filament\Resources\EntrepriseClientes\Schemas;

use App\Enums\CategorieDocumentGenere;
use App\Exceptions\DocumentGenerationException;
use App\Filament\Resources\EntrepriseClientes\Pages\ViewEntrepriseCliente;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use App\Models\EntrepriseFormation;
use App\Services\DocumentGenerationService;
use App\Services\GiacDocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class EntrepriseClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->description('Identité et activités principales de l\'entreprise')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('raison_sociale')
                            ->label('Raison Sociale')
                            ->weight(FontWeight::Bold)
                            ->columnSpan(2),

                        TextEntry::make('statut_juridique')
                            ->label('Statut Juridique')
                            ->badge(),

                        TextEntry::make('siege_social')
                            ->label('Siège Social')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),

                        TextEntry::make('date_creation')
                            ->label('Date de Création')
                            ->date('d/m/Y')
                            ->placeholder('—'),

                        TextEntry::make('effectif_total')
                            ->label('Effectif Total')
                            ->placeholder('—'),

                        TextEntry::make('secteur_activite')
                            ->label('Secteur d\'Activité'),

                        TextEntry::make('ice')
                            ->label('ICE')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('if')
                            ->label('Identifiant Fiscal (IF)')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('rc')
                            ->label('Registre de Commerce (RC)')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('patente')
                            ->label('Patente')
                            ->placeholder('—'),

                        TextEntry::make('num_cnss')
                            ->label('N° CNSS')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('region_affiliation_cnss')
                            ->label('Région d\'affiliation CNSS')
                            ->placeholder('—'),
                        TextEntry::make('activite')
                            ->label('Activité (Description détaillée)')
                            ->prose()
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Gérant')
                    ->description('Représentant légal de l\'entreprise')
                    ->icon('heroicon-o-user')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('gerant.nom')
                            ->label('Nom')
                            ->placeholder('—'),

                        TextEntry::make('gerant.prenom')
                            ->label('Prénom')
                            ->placeholder('—'),

                        TextEntry::make('gerant.genre')
                            ->label('Genre')
                            ->badge()
                            ->placeholder('—'),

                        TextEntry::make('gerant.cin')
                            ->label('CIN')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('gerant.fonction')
                            ->label('Fonction')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ])->columnSpanFull(),

                Section::make('Coordonnées & Contact')
                    ->description('Informations pour joindre le référent de l\'entreprise')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('contact_ref')
                            ->label('Nom du contact référent')
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make('email')
                            ->label('Adresse Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->columnSpanFull(),

                        TextEntry::make('telephone')
                            ->label('Téléphone')
                            ->icon('heroicon-o-phone')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('fax')
                            ->label('Fax')
                            ->placeholder('—'),
                    ])->columnSpanFull(),
                Section::make('Documents administratifs')
                    ->description('Pièces requises pour la constitution des dossiers de l\'entreprise cliente (GIAC, OFPPT...)')
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->collapsible()
                    ->columns(2)
                    ->schema(self::piecesJointesEntries()),

                Section::make('Visuels pour les documents générés')
                    ->description('Utilisées pour habiller le Modèle 6 et la Fiche de présence générés pour cette entreprise')
                    ->icon('heroicon-o-photo')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        ImageEntry::make('image_entete')
                            ->label("Image d'en-tête")
                            ->state(fn ($record) => $record->getEnteteImageBase64())
                            ->height(100)
                            ->placeholder('Aucune image fournie'),

                        ImageEntry::make('image_pied_page')
                            ->label('Image de pied de page')
                            ->state(fn ($record) => $record->getPiedPageImageBase64())
                            ->height(100)
                            ->placeholder('Aucune image fournie'),
                    ]),

                Section::make('Checklist GIAC — Dossier complet')
                    ->description("Pièces à joindre au dossier GIAC : les 7 pièces de l'entreprise sont à téléverser ici, les 5 pièces du cabinet proviennent de la fiche Organisme de Formation.")
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema(function (EntrepriseCliente $record): array {
                        $sections = [];

                        foreach (DossierGiac::PIECES_JOINTES as $key => $label) {
                            $statut = DossierGiac::pourEntreprise($record)->getPieceJointeStatut($key);

                            $sections[] = Section::make($label)
                                ->icon(match ($statut['etat']) {
                                    'Signé' => 'heroicon-o-check-circle',
                                    'En attente' => 'heroicon-o-clock',
                                    default => 'heroicon-o-exclamation-triangle',
                                })
                                ->iconColor(match ($statut['etat']) {
                                    'Signé' => 'success',
                                    'En attente' => 'warning',
                                    default => 'danger',
                                })
                                ->collapsible()
                                ->collapsed()
                                ->compact()
                                ->columns(3)
                                ->schema([
                                    TextEntry::make("checklist_entreprise_{$key}_etat")
                                        ->label('État')
                                        ->state($statut['etat'])
                                        ->badge()
                                        ->color(match ($statut['etat']) {
                                            'Signé' => 'success',
                                            'En attente' => 'warning',
                                            default => 'danger',
                                        }),

                                    TextEntry::make("checklist_entreprise_{$key}_nom_fichier")
                                        ->label('Nom du fichier')
                                        ->state($statut['nom_fichier'] ?? '—'),

                                    TextEntry::make("checklist_entreprise_{$key}_date_ajout")
                                        ->label("Date d'ajout")
                                        ->state($statut['date_ajout']?->format('d/m/Y') ?? '—'),

                                    Actions::make([
                                        Action::make("televerser_{$key}")
                                            ->label($statut['media'] ? 'Remplacer' : 'Téléverser')
                                            ->icon('heroicon-o-arrow-up-tray')
                                            ->color('gray')
                                            ->form([
                                                FileUpload::make('document')
                                                    ->label('Document (PDF / Image)')
                                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                                    ->maxSize(10240)
                                                    ->disk('local')
                                                    ->directory('giac-checklist-tmp')
                                                    ->required(),
                                            ])
                                            ->action(function (array $data, EntrepriseCliente $record) use ($key) {
                                                DossierGiac::pourEntreprise($record)
                                                    ->addMediaFromDisk($data['document'], 'local')
                                                    ->toMediaCollection($key);

                                                Notification::make()
                                                    ->success()
                                                    ->title('Document enregistré')
                                                    ->send();
                                            }),

                                        Action::make("telecharger_{$key}")
                                            ->label('Télécharger')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->color('gray')
                                            ->visible((bool) $statut['media'])
                                            ->action(function (EntrepriseCliente $record) use ($key) {
                                                $media = DossierGiac::pourEntreprise($record)->getFirstMedia($key);

                                                return response()->download($media->getPath(), $media->file_name);
                                            }),
                                    ])->columnSpanFull(),
                                ]);
                        }

                        foreach (EntrepriseFormation::PIECES_JOINTES as $key => $label) {
                            $organisme = EntrepriseFormation::current();
                            $statut = $organisme->getPieceJointeStatut($key);

                            $sections[] = Section::make($label)
                                ->description('Pièce du cabinet — gérée depuis Organisme de Formation')
                                ->icon(match ($statut['etat']) {
                                    'Valide' => 'heroicon-o-check-circle',
                                    'Expiré' => 'heroicon-o-x-circle',
                                    default => 'heroicon-o-exclamation-triangle',
                                })
                                ->iconColor(match ($statut['etat']) {
                                    'Valide' => 'success',
                                    'Expiré' => 'danger',
                                    default => 'warning',
                                })
                                ->collapsible()
                                ->collapsed()
                                ->compact()
                                ->columns(3)
                                ->schema([
                                    TextEntry::make("checklist_cabinet_{$key}_etat")
                                        ->label('État')
                                        ->state($statut['etat'])
                                        ->badge()
                                        ->color(match ($statut['etat']) {
                                            'Valide' => 'success',
                                            'Expiré' => 'danger',
                                            default => 'warning',
                                        }),

                                    TextEntry::make("checklist_cabinet_{$key}_nom_fichier")
                                        ->label('Nom du fichier')
                                        ->state($statut['nom_fichier'] ?? '—'),

                                    TextEntry::make("checklist_cabinet_{$key}_date_ajout")
                                        ->label("Date d'ajout")
                                        ->state($statut['date_ajout']?->format('d/m/Y') ?? '—'),

                                    Actions::make([
                                        Action::make("gerer_{$key}")
                                            ->label($statut['media'] ? 'Gérer dans les Paramètres' : 'Ajouter dans les Paramètres')
                                            ->icon('heroicon-o-arrow-top-right-on-square')
                                            ->color('gray')
                                            ->url(fn () => \App\Filament\Pages\ManageSettings::getUrl())
                                            ->openUrlInNewTab(),
                                    ])->columnSpanFull(),
                                ]);
                        }

                        return $sections;
                    }),

                Section::make('Archive des documents générés')
                    ->description('Historique complet des PDF générés pour cette entreprise (Modèle 5, Modèle 6, fiche d\'évaluation, GIAC, OFPPT...)')
                    ->icon('heroicon-o-archive-box')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->headerActions([
                        Action::make('filtrerDocumentsGeneres')
                            ->label('Filtrer')
                            ->icon('heroicon-o-funnel')
                            ->color('gray')
                            ->fillForm(fn (ViewEntrepriseCliente $livewire): array => [
                                'categorie' => $livewire->archiveDocumentsCategorie,
                                'genere_du' => $livewire->archiveDocumentsDateDebut,
                                'genere_au' => $livewire->archiveDocumentsDateFin,
                            ])
                            ->form([
                                Select::make('categorie')
                                    ->label('Catégorie')
                                    ->options(CategorieDocumentGenere::class)
                                    ->native(false)
                                    ->placeholder('Toutes les catégories'),

                                DatePicker::make('genere_du')
                                    ->label('Généré à partir du')
                                    ->native(false),

                                DatePicker::make('genere_au')
                                    ->label("Généré jusqu'au")
                                    ->native(false)
                                    ->afterOrEqual('genere_du'),
                            ])
                            ->action(function (array $data, ViewEntrepriseCliente $livewire) {
                                $livewire->archiveDocumentsCategorie = $data['categorie'] ?? null;
                                $livewire->archiveDocumentsDateDebut = $data['genere_du'] ?? null;
                                $livewire->archiveDocumentsDateFin = $data['genere_au'] ?? null;
                            }),

                        Action::make('reinitialiserFiltreDocumentsGeneres')
                            ->label('Réinitialiser')
                            ->icon('heroicon-o-x-mark')
                            ->color('gray')
                            ->visible(fn (ViewEntrepriseCliente $livewire): bool => filled($livewire->archiveDocumentsCategorie)
                                || filled($livewire->archiveDocumentsDateDebut)
                                || filled($livewire->archiveDocumentsDateFin))
                            ->action(function (ViewEntrepriseCliente $livewire) {
                                $livewire->archiveDocumentsCategorie = null;
                                $livewire->archiveDocumentsDateDebut = null;
                                $livewire->archiveDocumentsDateFin = null;
                            }),
                    ])
                    ->schema([
                        RepeatableEntry::make('documentsGeneres')
                            ->hiddenLabel()
                            ->state(function (EntrepriseCliente $record, ViewEntrepriseCliente $livewire) {
                                $query = $record->documentsGeneres();

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
                                TableColumn::make('Taille'),
                                TableColumn::make('')->hiddenHeaderLabel(),
                                TableColumn::make('')->hiddenHeaderLabel(),
                            ])
                            ->schema([
                                TextEntry::make('type_document'),
                                TextEntry::make('categorie')
                                    ->badge(),

                                TextEntry::make('version')
                                    ->formatStateUsing(fn (int $state): string => "v{$state}")
                                    ->color('indigo')
                                    ->badge(),

                                TextEntry::make('statut')
                                    //->color('violet')
                                    ->badge(),

                                TextEntry::make('genere_le')
                                    ->dateTime('d/m/Y H:i'),

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
                                Actions::make([
                                    Action::make('supprimer')
                                        ->label('')
                                        ->icon('heroicon-o-trash')
                                        ->color('danger')
                                        ->requiresConfirmation()
                                        ->modalHeading('Supprimer le document')
                                        ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.')
                                        ->action(function ($record, Action $action) {
                                            try {
                                                $record->delete();

                                                Notification::make()
                                                    ->success()
                                                    ->title('Document supprimé')
                                                    ->body('Le document a été supprimé avec succès.')
                                                    ->send();
                                            } catch (\Exception $e) {
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Erreur lors de la suppression')
                                                    ->body('Une erreur est survenue lors de la suppression du document.')
                                                    ->send();

                                                $action->halt();
                                            }
                                        }),
                                ])
                            ])
                            ->placeholder(fn (ViewEntrepriseCliente $livewire): string => (
                                filled($livewire->archiveDocumentsCategorie)
                                || filled($livewire->archiveDocumentsDateDebut)
                                || filled($livewire->archiveDocumentsDateFin)
                            )
                                ? 'Aucun document ne correspond aux critères sélectionnés.'
                                : 'Aucun document généré pour le moment.'),
                    ]),

                Actions::make([
                    ActionGroup::make(actions: [
                        Action::make('genererB1')
                            ->label('Bulletin d\'Adhésion')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('gray')
                            ->action(function (EntrepriseCliente $record, Action $action) {
                                try {
                                    $document = app(DocumentGenerationService::class)
                                        ->generateB1BulletinAdhesion($record);

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

                        Action::make('genererG7')
                            ->label('Bulletin Ré-adhésion')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('gray')
                            ->form([
                                TextInput::make('annee')
                                    ->label('Exercice (année)')
                                    ->numeric()
                                    ->required()
                                    ->default(now()->year)
                                    ->minValue(2000)
                                    ->maxValue(now()->year + 1),
                            ])
                            ->action(function (EntrepriseCliente $record, array $data, Action $action) {
                                try {
                                    $document = app(GiacDocumentGenerationService::class)
                                        ->generateBulletinReadhesion($record, (int)$data['annee']);

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
                        Action::make('genererC')
                            ->label('Fiche Entreprise (C)')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('gray')
                            ->action(function (EntrepriseCliente $record, Action $action) {
                                try {
                                    $document = app(DocumentGenerationService::class)
                                        ->generateCFicheEntreprise($record);

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

                        Action::make('genererG3')
                            ->label('Fiche de Renseignement')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('gray')
                            ->action(function (EntrepriseCliente $record, Action $action) {
                                try {
                                    $document = app(GiacDocumentGenerationService::class)
                                        ->generateFicheOrganismeConseil($record);

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

                        Action::make('genererF3')
                            ->label('Fiche d\'identification de l\'organisme')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('gray')
                            ->action(function (EntrepriseCliente $record, Action $action) {
                                try {
                                    $document = app(GiacDocumentGenerationService::class)
                                        ->generateF3FicheIdentificationOrganisme($record);

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

                        Action::make('genererDeclarationHonneur')
                            ->label('Déclaration sur l\'Honneur')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('gray')
                            ->action(function (EntrepriseCliente $record, Action $action) {
                                try {
                                    $document = app(DocumentGenerationService::class)
                                        ->generateGDeclarationHonneur($record);

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
                    ])->button()
                ])->alignEnd()->columnSpanFull(),
            ]);
    }
    protected static function piecesJointesEntries(): array
    {
        $entries = [];

        foreach (EntrepriseCliente::PIECES_JOINTES as $key => $label) {
            $entries[] = TextEntry::make($key . '_statut')
                ->label($label)
                ->state(fn (EntrepriseCliente $record) => $record->getPieceJointeStatut($key))
                ->badge()
                ->color(fn (EntrepriseCliente $record) => $record->hasMedia($key) ? 'success' : 'danger')
                ->icon(fn (EntrepriseCliente $record) => $record->hasMedia($key)
                    ? 'heroicon-o-check-circle'
                    : 'heroicon-o-exclamation-triangle')
                ->url(fn (EntrepriseCliente $record) => $record->hasMedia($key)
                    ? $record->getFirstMediaUrl($key)
                    : null)
                ->openUrlInNewTab();
        }

        return $entries;
    }
}
