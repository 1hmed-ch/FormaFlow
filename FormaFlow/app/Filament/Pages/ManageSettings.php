<?php

namespace App\Filament\Pages;

use App\Models\EntrepriseFormation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Actions;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedBuildingLibrary;
    protected static ?int $navigationSort = 100;
    protected static ?string $title = 'Organisme de Formation';
    protected static string|null|\UnitEnum $navigationGroup = 'Paramètres de l\'Organisme';
    protected static ?string $navigationLabel = 'Organisme de Formation';
    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    /**
     * Sous-ensemble de PIECES_JOINTES géré sur CETTE page.
     * Les autres (fiche_identification, fiche_renseignement) sont
     * gérées ailleurs — on ne les touche pas ici.
     */
    protected const PIECES_JOINTES_PAGE = [
        'rc_modele_j',
        'cv_consultants',
        'proposition_intervention',
        'eligibilite_csf',
    ];

    /**
     * Retourne uniquement le sous-ensemble de PIECES_JOINTES pertinent
     * pour cette page (garde label + multiple depuis le modèle).
     */
    protected static function piecesJointesPage(): array
    {
        return array_intersect_key(
            EntrepriseFormation::PIECES_JOINTES,
            array_flip(self::PIECES_JOINTES_PAGE)
        );
    }

    public function mount(): void
    {
        $record = EntrepriseFormation::current();

        $initialData = $record->toArray();

        foreach (array_keys(self::piecesJointesPage()) as $collection) {
            $status = $record->getPieceJointeStatut($collection);
            $initialData['date_expiration_' . $collection] = $status['date_expiration'] ?? null;
        }

        $this->form->fill($initialData);
    }

    public function form(Schema $schema): Schema
    {
        $piecesJointesFields = [];
        $record = EntrepriseFormation::current();

        $makeSection = function (string $key, string $label, bool $isMultiple) use ($record) {
            if ($isMultiple) {
                $mediaCollection = $record->getMedia($key);
                $isUploaded = $mediaCollection->isNotEmpty();

                $icon = $isUploaded ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle';
                $color = $isUploaded ? 'success' : 'warning';
                $description = $isUploaded ? $mediaCollection->count() . ' fichier(s) fourni(s)' : 'Document manquant';

                $fieldsSchema = [
                    SpatieMediaLibraryFileUpload::make($key)
                        ->label('Documents (PDF / Image)')
                        ->collection($key)
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->multiple()
                        ->maxSize(10240)
                        ->visibility('private')
                        ->model($record)
                        ->columnSpanFull(),
                ];
            } else {
                $media = $record->getFirstMedia($key);
                $isUploaded = $media !== null;
                $dateExpiration = $isUploaded ? $media->getCustomProperty('date_expiration') : null;
                $isExpired = $isUploaded && $dateExpiration && \Illuminate\Support\Carbon::parse($dateExpiration)->isPast();

                if (!$isUploaded) {
                    $icon = 'heroicon-o-exclamation-triangle';
                    $color = 'warning';
                    $description = 'Document manquant';
                } elseif ($isExpired) {
                    $icon = 'heroicon-o-x-circle';
                    $color = 'danger';
                    $description = 'Document expiré';
                } else {
                    $icon = 'heroicon-o-check-circle';
                    $color = 'success';
                    $description = 'Document fourni • Ajouté le ' . $media->created_at->format('d/m/Y');
                }

                $fieldsSchema = [
                    SpatieMediaLibraryFileUpload::make($key)
                        ->label('Document (PDF / Image)')
                        ->collection($key)
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240)
                        ->visibility('private')
                        ->model($record),

                    DatePicker::make('date_expiration_' . $key)
                        ->label("Date d'expiration")
                        ->native(false),
                ];
            }

            return Section::make($label)
                ->icon($icon)
                ->iconColor($color)
                ->description($description)
                ->collapsible()
                ->collapsed(true)
                ->compact()
                ->columns(2)
                ->schema($fieldsSchema);
        };

        // Génération dynamique — uniquement les 3 pièces de cette page
        foreach (self::piecesJointesPage() as $key => $config) {
            $piecesJointesFields[] = $makeSection($key, $config['label'], $config['multiple']);
        }

        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Informations Générales')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('raison_sociale')->label('Raison Sociale')->required(),
                                Grid::make(2)->schema([
                                    TextInput::make('gerant_nom')->label('Nom du Gérant'),
                                    TextInput::make('gerant_prenom')->label('Prénom du Gérant'),
                                ]),
                                Grid::make(2)->schema([
                                    DatePicker::make('date_creation')->label('Date de Création'),
                                    Select::make('statut_juridique')
                                        ->label('Statut Juridique')
                                        ->options([
                                            'SARL' => 'SARL',
                                            'SARL AU' => 'SARL AU',
                                            'SA' => 'SA',
                                            'SNC' => 'SNC',
                                            'Auto-entrepreneur' => 'Auto-entrepreneur',
                                        ])
                                        ->searchable()
                                        ->native(false),
                                ]),
                                TextInput::make('activite')->label('Activité'),
                                TextInput::make('siege_social')->label('Siège Social'),
                                TextInput::make('ville')->label('Ville'),
                            ]),
                        Section::make('Visuels')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('logo')
                                    ->label('Logo Organisme')
                                    ->image()
                                    ->directory('logos')
                                    ->maxSize(5120),
                            ]),
                    ]),
                Section::make('Identifiants Fiscaux & Coordonnées')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('ice')->label('ICE'),
                            TextInput::make('rc')->label('RC'),
                            TextInput::make('if')->label('N° IF'),
                            TextInput::make('patente')->label('Patente'),
                        ]),
                        Grid::make(4)->schema([
                            TextInput::make('cnss')->label('N° CNSS'),
                            TextInput::make('capital_social')->label('Capital Social'),
                            TextInput::make('telephone')->label('Téléphone'),
                            TextInput::make('fax')->label('Fax'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('email')->label('Email de Contact')->email(),
                            TextInput::make('site_web')->label('Site Web')->url(),
                        ]),
                    ]),
                Section::make('Compétences & Moyens')
                    ->schema([
                        TagsInput::make('domaines_competence')
                            ->label('Domaines de Compétence')
                            ->placeholder('Ajouter un domaine...'),
                        TagsInput::make('moyens_pedagogiques')
                            ->label('Moyens Pédagogiques')
                            ->placeholder('Ajouter un moyen...'),
                    ]),
                Section::make('Ressources Humaines (Effectifs)')
                    ->description("Le détail \"dont étrangers\" alimente les fiches G3 (GIAC) et Formulaire F3 (OFPPT).")
                    ->schema([
                        Grid::make(2)->schema([
                            Fieldset::make('Experts Permanents')->schema([
                                TextInput::make('nb_experts_permanents')->label('Effectif')->numeric()->minValue(0)->default(0),
                                TextInput::make('nb_experts_permanents_etrangers')->label('Dont étrangers')->numeric()->minValue(0)->default(0),
                            ]),
                            Fieldset::make('Experts Vacataires')->schema([
                                TextInput::make('nb_experts_vacataires')->label('Effectif')->numeric()->minValue(0)->default(0),
                                TextInput::make('nb_experts_vacataires_etrangers')->label('Dont étrangers')->numeric()->minValue(0)->default(0),
                            ]),
                            Fieldset::make('Animateurs / Formateurs')->schema([
                                TextInput::make('nb_animateurs_formateurs')->label('Effectif')->numeric()->minValue(0)->default(0),
                                TextInput::make('nb_animateurs_formateurs_etrangers')->label('Dont étrangers')->numeric()->minValue(0)->default(0),
                            ]),
                            Fieldset::make('Autres Employés')->schema([
                                TextInput::make('nb_autres_employes')->label('Effectif')->numeric()->minValue(0)->default(0),
                                TextInput::make('nb_autres_employes_etrangers')->label('Dont étrangers')->numeric()->minValue(0)->default(0),
                            ]),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('effectif_total')
                                ->label('Effectif Total')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),

                            Toggle::make('appartient_groupe_etranger')
                                ->label("L'organisme appartient à un groupe étranger")
                                ->inline(false)
                                ->live(),
                        ]),

                        TextInput::make('nom_groupe_etranger')
                            ->label('Si oui lequel')
                            ->placeholder('Préciser le nom du groupe...')
                            ->visible(fn (callable $get) => $get('appartient_groupe_etranger') === true)
                            ->columnSpanFull(),

                        Textarea::make('references')
                            ->label('Références')
                            ->placeholder('Saisir les références ici...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Représentant Légal')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('representant_nom')->label('Nom du Représentant'),
                            TextInput::make('representant_fonction')->label('Fonction du Représentant'),
                        ]),
                    ]),

                Section::make('Pièces Jointes de l\'organisme')
                    ->description('Veuillez glisser-déposer vos documents administratifs requis.')
                    ->schema([
                        Grid::make(2)->schema($piecesJointesFields)
                    ]),

                Actions::make([
                    Action::make('save')
                        ->label('Enregistrer')
                        ->action('save')
                        ->color('primary')
                        ->keyBindings(['mod+s']),
                ])
                    ->alignEnd(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $record = EntrepriseFormation::current();
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            if ($value === '') {
                $state[$key] = null;
            }
        }

        try {
            $record->update($state);

            foreach (self::piecesJointesPage() as $collection => $config) {
                if ($config['multiple']) {
                    continue; // les collections multiples n'ont pas de date d'expiration
                }

                $media = $record->getFirstMedia($collection);
                if ($media) {
                    $expirationDate = $state['date_expiration_' . $collection] ?? null;
                    $media->setCustomProperty('date_expiration', $expirationDate);
                    $media->save();
                }
            }

            Notification::make()
                ->title('Paramètres enregistrés avec succès !')
                ->success()
                ->send();

        } catch (\Illuminate\Database\QueryException $e) {
            Notification::make()
                ->title('Erreur de base de données')
                ->body('Une contrainte a empêché l\'enregistrement. Vérifiez les champs uniques (ICE, RC...).')
                ->danger()
                ->send();
            report($e);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Une erreur est survenue')
                ->body('L\'enregistrement a échoué.')
                ->danger()
                ->send();
            report($e);
        }
    }
}