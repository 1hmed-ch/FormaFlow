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
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;



class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 100;
    protected static ?string $title = 'Paramètres de l\'organisme';
    protected static ?string $navigationLabel = 'Paramètres';
    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

 public function mount(): void
    {
        $record = EntrepriseFormation::current();
        $this->form->fill($record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        // Section 1: Infos Générales
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
                            ]),

                        // Section Visuels (Logo & Signature)
                        Section::make('Visuels')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('logo')
                                    ->label('Logo Organisme')
                                    ->image()
                                    ->directory('logos')
                                    ->maxSize(5120),

                                FileUpload::make('signature')
                                    ->label('Signature Officielle')
                                    ->image()
                                    ->directory('signatures')
                                    ->maxSize(5120)
                                    ->visibility('private'),
                        ]),
                ]),

            // Section 2: Identifiants Fiscaux
            Section::make('Identifiants Fiscaux & Coordonnées')
                ->schema([
                    Grid::make(4)->schema([
                        TextInput::make('ice')->label('ICE')->required(),
                        TextInput::make('rc')->label('RC')->required(),
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

            // Section 3: Domaines (JSON tags)
            Section::make('Compétences & Moyens')
                ->schema([
                    TagsInput::make('domaines_competence')
                        ->label('Domaines de Compétence')
                        ->placeholder('Ajouter un domaine...'),
                    TagsInput::make('moyens_pedagogiques')
                        ->label('Moyens Pédagogiques')
                        ->placeholder('Ajouter un moyen...'),
                ]),

            // Section 4: RH Effectifs
            Section::make('Ressources Humaines (Effectifs)')
                ->schema([
                    Grid::make(5)->schema([
                        TextInput::make('nb_experts_permanents')->label('Exp. Permanents')->numeric()->minValue(0),
                        TextInput::make('nb_experts_vacataires')->label('Exp. Vacataires')->numeric()->minValue(0),
                        TextInput::make('nb_animateurs_formateurs')->label('Formateurs')->numeric()->minValue(0),
                        TextInput::make('nb_autres_employes')->label('Autres Employés')->numeric()->minValue(0),
                        TextInput::make('effectif_total')->label('Effectif Total')->numeric()->minValue(0),
                    ]),
                ]),

            // Section 5: Représentant Légal
            Section::make('Représentant Légal')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('representant_nom')->label('Nom du Représentant'),
                        TextInput::make('representant_fonction')->label('Fonction du Représentant'),
                    ]),
                ]),
        ])
        ->statePath('data');
}

   public function save(): void
    {
        $record = EntrepriseFormation::current();

        try {
            $record->update($this->form->getState());

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
                ->body('L\'enregistrement a échoué. Réessayez ou contactez le support.')
                ->danger()
                ->send();

            report($e);
        }
    }
}