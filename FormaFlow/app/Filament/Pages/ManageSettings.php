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

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms , InteractsWithActions ;

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

                        // Section Visuels (Logo )
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
   public function uploadPieceAction(): Action
    {
        return Action::make('uploadPiece')
            ->label('Ajouter / Modifier')
            ->modalHeading(fn (array $arguments) => EntrepriseFormation::PIECES_JOINTES[$arguments['collection']])
            ->fillForm(function (array $arguments) {
                $data = EntrepriseFormation::current()->getPieceJointeStatut($arguments['collection']);
                return ['date_expiration' => $data['date_expiration'] ?? null];
            })
            ->schema(fn (array $arguments) => [
                SpatieMediaLibraryFileUpload::make('fichier')
                    ->label('Fichier')
                    ->collection($arguments['collection'])
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(10240)
                    ->visibility('private')
                    ->model(EntrepriseFormation::current())
                    ->required(fn () => !EntrepriseFormation::current()->hasMedia($arguments['collection'])),
                DatePicker::make('date_expiration')
                    ->label("Date d'expiration (laisser vide si non applicable)")
                    ->native(false),
            ])
            ->action(function (array $data, array $arguments) {
                $record = EntrepriseFormation::current();
                $media = $record->getFirstMedia($arguments['collection']);

                if ($media) {
                    $media->setCustomProperty('date_expiration', $data['date_expiration'] ?? null);
                    $media->save();
                }

                Notification::make()->title('Document mis à jour')->success()->send();
            });
    }

    public function getPiecesJointesProperty(): array
    {
        $record = EntrepriseFormation::current();
        return collect(EntrepriseFormation::PIECES_JOINTES)
            ->mapWithKeys(fn ($label, $key) => [
                $key => array_merge(['label' => $label], $record->getPieceJointeStatut($key)),
            ])
            ->toArray();
    }
}
