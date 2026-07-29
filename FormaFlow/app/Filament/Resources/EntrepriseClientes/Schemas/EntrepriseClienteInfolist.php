<?php

namespace App\Filament\Resources\EntrepriseClientes\Schemas;

use App\Exceptions\DocumentGenerationException;
use App\Models\EntrepriseCliente;
use App\Services\DocumentGenerationService;
use App\Services\GiacDocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                Section::make('Archive des documents générés')
                    ->description('Historique complet des PDF générés pour cette entreprise (Modèle 5, Modèle 6, fiche d\'évaluation, GIAC, OFPPT...)')
                    ->icon('heroicon-o-archive-box')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('documentsGeneres')
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
                            ])
                            ->placeholder('Aucun document généré pour le moment.'),
                    ]),

                Actions::make([
                    ActionGroup::make(actions: [
                        Action::make('genererModele6')
                            ->label('Modèle 6')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('gray')
                            ->form([
                                TextInput::make('annee')
                                    ->label('Exercice (année)')
                                    ->numeric()
                                    ->required()
                                    ->default(now()->year)
                                    ->minValue(2000)
                                    ->maxValue(now()->year),
                            ])
                            ->action(function (EntrepriseCliente $record, array $data, Action $action) {
                                try {
                                    $document = app(DocumentGenerationService::class)
                                        ->generateModele6($record, (int)$data['annee']);

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

                        // 2. Bulletin d'Adhésion (B1)
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

                        // 4. Fiche Entreprise (C)
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

                        // 7. Déclaration sur l'Honneur
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
