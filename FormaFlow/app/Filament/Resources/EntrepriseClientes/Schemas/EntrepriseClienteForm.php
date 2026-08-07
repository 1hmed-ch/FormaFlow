<?php

namespace App\Filament\Resources\EntrepriseClientes\Schemas;

use App\Enums\gerantGender;
use App\Models\EntrepriseCliente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;

class EntrepriseClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->description('Identité et activités principales de l\'entreprise')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        TextInput::make('raison_sociale')
                            ->label('Raison Sociale')
                            ->required()
                            ->maxLength(255),

                        Select::make('statut_juridique')
                            ->label('Statut Juridique')
                            ->options([
                                'SARL' => 'SARL',
                                'SARL AU' => 'SARL AU',
                                'SA' => 'SA',
                                'SNC' => 'SNC',
                                'Auto-entrepreneur' => 'Auto-entrepreneur',
                            ])
                            ->searchable(),

                        TextInput::make('siege_social')
                            ->label('Siège Social')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                       TextInput::make('ville')
                            ->label('Ville')
                            ->required(),

                        DatePicker::make('date_creation')
                            ->label('Date de Création')
                            ->displayFormat('d/m/Y')
                            ->native(false),

                        TextInput::make('effectif_total')
                            ->label('Effectif Total')
                            ->numeric()
                            ->minValue(1),

                        TextInput::make('secteur_activite')
                            ->label('Secteur d\'Activité')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('activite')
                            ->label('Activité (Description détaillée)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Répartition de l\'Effectif')
                    ->description('Détail des catégories socioprofessionnelles pour les dossiers GIAC')
                    ->icon('heroicon-o-user-group')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        TextInput::make('effectif_cadre')
                            ->label('Cadres')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        TextInput::make('effectif_cadre_moyen')
                            ->label('Cadres Moyens')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        TextInput::make('effectif_agent_qualifie')
                            ->label('Agents Qualifiés')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        TextInput::make('effectif_agent_sans_qualification')
                            ->label('Agents Sans Qualification')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        TextInput::make('effectif_agent_occasionnel')
                            ->label('Agents Occasionnels')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columnSpanFull(),

                Section::make('Gérant')
                        ->description('Représentant légal de l\'entreprise')
                        ->icon('heroicon-o-user')
                        ->relationship('gerant')
                        ->schema([

                            TextInput::make('nom')
                                ->label('Nom')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('prenom')
                                ->label('Prénom')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('fonction')
                                ->label('Fonction / Qualité')
                                ->placeholder('ex: Gérant, Directeur Général...')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('cin')
                                ->label('CIN')
                                ->required()
                                ->maxLength(20)
                                ->unique(table: 'gerants', column: 'cin', ignoreRecord: true),


                            TextInput::make('email')
                                ->label('Adresse E-mail')
                                ->email()
                                ->placeholder('ex: gerant@entreprise.ma')
                                ->maxLength(255),

                            /*Select::make('genre')
                                ->label('Genre')
                                ->options(gerantGender::class)
                                ->required()
                                ->native(false),*/
                            ToggleButtons::make('genre')
                                ->label('Genre')
                                ->options(GerantGender::class)
                                ->colors([
                                    GerantGender::Homme->value => 'primary',
                                    GerantGender::Femme->value => 'pink',
                                ])
                                ->required()
                                ->inline(),
                            TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                Section::make('Codes d\'accès Plateforme OFPPT')
                        ->description('Informations d\'inscription (Gmail) et de connexion à la plateforme')
                        ->icon('heroicon-o-key')
                        ->columns(2)
                        ->schema([
                            // 1. Gmail d'inscription
                            TextInput::make('gmail_login_ofppt')
                                ->label('Gmail - Login')
                                ->email()
                                ->maxLength(255),

                            TextInput::make('gmail_ofppt_mdp')
                                ->label('Gmail - Mot de passe')
                                ->password()
                                ->revealable()
                                ->maxLength(255),
                            TextInput::make('ofppt_mdp')
                                ->label('Plateforme - Mot de passe')
                                ->password()
                                ->revealable()
                                ->maxLength(255),
                        ])->columnSpanFull(),
                Section::make('Informations du Chèque ')
                        ->schema([
                            TextInput::make('cheque_banque')
                                ->label('Banque')
                                ->nullable(),
                            TextInput::make('cheque_numero')
                                ->label('N° de Chèque')
                                ->nullable(),
                            DatePicker::make('cheque_date')
                                ->label('Date du Chèque')
                                ->native(false)
                                ->nullable(),
                                ])->columns(3)->columnSpanFull(),
            Section::make('Identifiants Légaux & Administratifs')
                    ->description('Numéros d\'immatriculation légaux (ICE, IF, RC...)')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('ice')
                            ->label('ICE')
                            ->required()
                            ->length(15)
                            ->placeholder('Ex: 123456789012345'),

                        TextInput::make('if')
                            ->label('Identifiant Fiscal (IF)')
                            ->placeholder('Ex: 23456789')
                            ->required()
                            ->numeric()
                            ->maxLength(20),

                        TextInput::make('rc')
                            ->label('Registre de Commerce (RC)')
                            ->maxLength(50),

                        TextInput::make('patente')
                            ->label('Patente')
                            ->maxLength(50),

                        TextInput::make('num_cnss')
                            ->label('N° CNSS')
                            ->placeholder('Ex: 8253792')
                            ->numeric()
                            ->maxLength(20),

                        TextInput::make('region_affiliation_cnss')
                            ->label('Région d\'affiliation CNSS')
                            ->maxLength(100),
                    ])->columnSpanFull(),
                Section::make('Informations Financières & Historique GIAC')
                    ->description('Renseignements complémentaires nécessaires pour la Fiche d’Information sur l’entreprise du dossier GIAC')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('montant_tfp')
                                    ->label('Montant de la Taxe versée l\'année précédente')
                                    ->numeric()
                                    ->prefix('DH')
                                    ->placeholder('Ex: 15000.00'),

                                Toggle::make('deja_depose_giac')
                                    ->label('Avez-vous déjà déposé une demande auprès d\'un GIAC ?')
                                    ->inline(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state === false) {
                                            $set('nom_ancien_giac', null);
                                            $set('date_depot_ancien_giac', null);
                                        }
                                    }),
                            ]),

                        Grid::make(2)
                            ->visible(fn (callable $get) => $get('deja_depose_giac') === true)
                            ->schema([
                                TextInput::make('nom_ancien_giac')
                                    ->label('Quel GIAC ?')
                                    ->placeholder('Ex: GIAC Technologies...'),
                                DatePicker::make('date_depot_ancien_giac')
                                    ->label('Date de dépôt de ce dossier')
                                    ->displayFormat('d/m/Y')
                                    ->native(false),
                            ]),
                    ]),
                Section::make('Coordonnées & Contact')
                    ->description('Informations pour joindre le référent de l\'entreprise')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('contact_ref')
                            ->label('Nom du contact référent')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Adresse Email')
                            ->email()
                            ->required()
                            ->placeholder("EX: entreprise@example.com")
                            ->columnSpanFull()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+212 6 00 00 00 00'),

                        TextInput::make('fax')
                            ->label('Fax')
                            ->tel()
                            ->placeholder('+212 5 00 00 00 00')
                            ->maxLength(20),
                    ])->columnSpanFull(),

                /*Section::make('Documents administratifs à joindre')
                            ->description('Pièces requises pour la constitution des dossiers de l\'entreprise cliente (GIAC, OFPPT...)')
                            ->icon('heroicon-o-paper-clip')
                            ->columns(2)
                            ->collapsible()
                            ->schema(self::piecesJointesFields())
                            ->columnSpanFull(),
                Section::make('Autres documents')
                            ->description('Documents complémentaires avec intitulé libre')
                            ->icon('heroicon-o-document-duplicate')
                            ->collapsible()
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('autres_documents_repeater')
                                    ->label('Nouveaux documents à ajouter')
                                    ->default([])
                                    ->schema([
                                        TextInput::make('intitule')
                                            ->label('Intitulé du document')
                                            ->required()
                                            ->maxLength(255),

                                        FileUpload::make('fichier')
                                            ->label('Fichier')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(10240)
                                            ->disk('local')
                                            ->directory('tmp-autres-documents')
                                            ->visibility('private')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Ajouter un document')
                                    ->reorderable(false),
                            ]),*/
        ]);
    }

    /*protected static function piecesJointesFields(): array
    {
        $fields = [];

        foreach (EntrepriseCliente::PIECES_JOINTES as $key => $label) {
            $isMultiple = $key === 'autres_documents';

            $fields[] = Section::make($label)
                ->icon(fn (?EntrepriseCliente $record) => $record?->hasMedia($key)
                    ? 'heroicon-o-check-circle'
                    : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (?EntrepriseCliente $record) => $record?->hasMedia($key)
                    ? 'success'
                    : 'warning')
                ->description(fn (?EntrepriseCliente $record) => $record?->getPieceJointeStatut($key) ?? 'Manquant')
                ->collapsible()
                ->collapsed(true)
                ->compact()
                ->schema([
                    SpatieMediaLibraryFileUpload::make($key)
                        ->label($isMultiple ? 'Documents (PDF / Image)' : 'Document (PDF / Image)')
                        ->collection($key)
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->multiple($isMultiple)
                        ->maxSize(10240)
                        ->visibility('private')
                        ->hiddenLabel(),
                ]);
        }

        return $fields;
    }*/
}
