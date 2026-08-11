<?php

namespace App\Filament\Resources\Formations\Schemas;

use App\Enums\DemandeFinancementStatus;
use App\Filament\Concerns\HasPiecesJointesCards;
use App\Models\Formation;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput as FormTextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Page "Documents à joindre" d'une Formation : reprend les sections de
 * pièces jointes signées et de financement OFPPT de l'Archive, mais
 * accessibles directement depuis la Formation. Les documents restent
 * physiquement rattachés à l'EntrepriseCliente de la formation (DossierGiac
 * / EntrepriseFormation) : toutes les formations d'une même entreprise
 * partagent donc le même dossier, seul le point d'accès change.
 */
class FormationDocumentsInfolist
{
    use HasPiecesJointesCards;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fiche formation')
                    ->description('Formation et entreprise cliente concernées')
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('intitule')
                            ->label('Formation :')
                            ->weight('bold'),
                        TextEntry::make('entrepriseCliente.raison_sociale')
                            ->label('Entreprise :')
                            ->weight('bold'),
                    ])
                    ->columns(2),

                Section::make('Documents à joindre')
                    ->description("Pièces spécifiques à cette formation")
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                            ->schema(self::piecesAJoindreFormationCards())
                            ->columnSpanFull(),
                    ]),

                Section::make('Documents signés et Checklist')
                    ->description('Pièces signées par l\'entreprise et par le cabinet, rattachées au dossier de l\'entreprise')
                    ->icon('heroicon-o-building-office')
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Documents signés (Entreprise)')
                            ->description('Pièces de la checklist signées par l\'entreprise')
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                                    ->schema(self::checklistGiacCards())
                                    ->columnSpanFull(),
                            ])
                            ->compact(),

                        Section::make('Documents signés (Organisme de formation)')
                            ->description('Pièces signées du cabinet')
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                                    ->schema(self::piecesJointesCabinetCards())
                                    ->columnSpanFull(),
                            ])
                            ->compact(),
                    ]),

                Section::make('Demande de Financement (OFPPT)')
                    ->description('Suivi du dossier de financement OFPPT de l\'entreprise')
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
                                ->fillForm(fn (Formation $record) => [
                                    'statut_demande_financement' => $record->entrepriseCliente?->statut_demande_financement,
                                ])
                                ->action(function (Formation $record, array $data) {
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
                            ->state(fn (Formation $record) => self::resolveDossierGiac($record)?->getMedia('autres_documents_ofppt') ?? collect())
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
                                        Action::make('voirAutreDocOfpptFormation')
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
                                        Action::make('supprimerAutreDocOfpptFormation')
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
                            Action::make('modalAjouterAutreDocOfpptFormation')
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
                                ->action(function (Formation $record, array $data, $livewire) {
                                    $dossier = self::resolveDossierGiac($record);
                                    if (! $dossier) {
                                        return;
                                    }

                                    $dossier
                                        ->addMediaFromDisk($data['upload_autre_doc_ofppt'], 'local')
                                        ->withCustomProperties(['intitule' => $data['nouvel_intitule']])
                                        ->toMediaCollection('autres_documents_ofppt');

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
