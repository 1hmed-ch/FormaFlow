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
                    ->columns(2)
                    ->schema([
                        TextEntry::make('entete_page_statut')
                            ->label("Image d'en-tête")
                            ->state(fn ($record) => $record->hasMedia('entete_page') ? 'Disponible' : 'Non disponible')
                            ->badge()
                            ->color(fn ($record) => $record->hasMedia('entete_page') ? 'success' : 'orange')
                            ->icon(fn ($record) => $record->hasMedia('entete_page')
                                ? 'heroicon-o-check-circle'
                                : 'heroicon-o-exclamation-triangle'),

                        TextEntry::make('pied_page_statut')
                            ->label('Image de pied de page')
                            ->state(fn ($record) => $record->hasMedia('pied_page') ? 'Disponible' : 'Non disponible')
                            ->badge()
                            ->color(fn ($record) => $record->hasMedia('pied_page') ? 'success' : 'orange')
                            ->icon(fn ($record) => $record->hasMedia('pied_page')
                                ? 'heroicon-o-check-circle'
                                : 'heroicon-o-exclamation-triangle'),

                        Actions::make([
                            Action::make('voir_image_entete')
                                ->label("Voir l'en-tête")
                                ->icon('heroicon-o-eye')
                                ->color('info')
                                ->outlined()
                                ->size('sm')
                                ->visible(fn ($record) => $record->hasMedia('entete_page'))
                                ->modalHeading("Image d'en-tête")
                                ->modalContent(fn ($record) => view('filament.modals.apercu-fichier', [
                                    'url' => route('media.stream', $record->getFirstMedia('entete_page')),
                                    'mime' => $record->getFirstMedia('entete_page')?->mime_type ?? 'image/png',
                                ]))
                                ->modalSubmitAction(false)
                                ->modalCancelAction(false)
                                ->modalWidth('4xl'),

                            Action::make('voir_image_pied_page')
                                ->label('Voir le pied de page')
                                ->icon('heroicon-o-eye')
                                ->color('info')
                                ->outlined()
                                ->size('sm')
                                ->visible(fn ($record) => $record->hasMedia('pied_page'))
                                ->modalHeading('Image de pied de page')
                                ->modalContent(fn ($record) => view('filament.modals.apercu-fichier', [
                                    'url' => route('media.stream', $record->getFirstMedia('pied_page')),
                                    'mime' => $record->getFirstMedia('pied_page')?->mime_type ?? 'image/png',
                                ]))
                                ->modalSubmitAction(false)
                                ->modalCancelAction(false)
                                ->modalWidth('4xl'),
                        ])->columnSpanFull(),
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
                            ->label('Fiche d\'Entreprise')
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
                            ->label('Fiche ID de l\'organisme')
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

            $pieces = array_filter(
            EntrepriseCliente::PIECES_JOINTES,
            fn ($key) => $key !== 'facture_pro_forma',
            ARRAY_FILTER_USE_KEY
        );

        foreach ($pieces as $key => $label) {
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
