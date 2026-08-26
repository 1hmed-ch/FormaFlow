<?php

namespace App\Filament\Concerns;

use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use App\Models\EntrepriseFormation;
use App\Models\Formation;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;

/**
 * Sections de pièces jointes (checklist GIAC signée par l'entreprise,
 * pièces du cabinet, pièces de financement OFPPT) réutilisées à la fois
 * par l'Archive (rattachée à un DossierGiac) et par la page "Documents à
 * joindre" d'une Formation. Ces documents restent stockés au niveau de
 * l'EntrepriseCliente (via DossierGiac / EntrepriseFormation) : $record
 * n'a besoin que d'exposer une relation entrepriseCliente(), ce qui est
 * vrai aussi bien pour DossierGiac que pour Formation.
 */
trait HasPiecesJointesCards
{
    /**
     * Résout le DossierGiac qui porte réellement les médias pour $record :
     * - si $record est une Formation, chaque formation a sa propre ligne
     *   (et donc ses propres pièces jointes) ;
     * - si $record est déjà un DossierGiac (page Archive, niveau
     *   entreprise), on le renvoie tel quel.
     */
    protected static function resolveMediaOwner(Model $record): ?Model
    {
        return match (true) {
            $record instanceof Formation => $record, // les pièces vivent DIRECTEMENT sur la Formation
            $record instanceof DossierGiac => $record,
            default => $record->entrepriseCliente ? DossierGiac::pourEntreprise($record->entrepriseCliente) : null,
        };
    }
    protected static function resolveCabinetMediaOwner(Model $record): Model
    {
        return $record instanceof Formation ? $record : EntrepriseFormation::current();
    }

    protected static function checklistGiacCards(): array
    {
        $cards = [];

        foreach (DossierGiac::PIECES_JOINTES as $key => $label) {
            $getStatut = fn (Model $record) =>self::resolveMediaOwner($record)
                ?->getPieceJointeStatut($key)
                ?? ['etat' => 'Manquant', 'media' => null, 'nom_fichier' => null, 'date_ajout' => null];

            $cards[] = Section::make($label)
                ->icon(fn (Model $record) => $getStatut($record)['media'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (Model $record) => $getStatut($record)['media'] ? 'success' : 'warning')
                ->description(fn (Model $record) => $getStatut($record)['media'] ? 'Déposé' : 'Manquant')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('date_ajout_checklist_' . $key)
                        ->label("Date d'ajout")
                        ->state(fn (Model $record) => $getStatut($record)['date_ajout']?->format('d/m/Y') ?? '—'),

                    Actions::make([
                        Action::make('voir_checklist_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible(fn (Model $record) => (bool) $getStatut($record)['media'])
                            ->modalHeading($label)
                            ->modalContent(function (Model $record) use ($key, $getStatut) {
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
                            ->visible(fn (Model $record) => (bool) $getStatut($record)['media'])
                            ->action(function (Model $record) use ($getStatut) {
                                $media = $getStatut($record)['media'];
                                if (! $media || ! file_exists($media->getPath())) {
                                    Notification::make()->danger()->title('Fichier introuvable')->send();
                                    return;
                                }

                                return response()->download($media->getPath(), $media->file_name);
                            }),

                        Action::make('gerer_checklist_' . $key)
                            ->label(fn (Model $record) => $getStatut($record)['media'] ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color(fn (Model $record) => $getStatut($record)['media'] ? 'gray' : 'primary')
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
                            ->action(function (array $data, Model $record, $livewire) use ($key, $label) {
                                $dossier = self::resolveMediaOwner($record);
                                if (! $dossier) {
                                    return;
                                }

                                $dossier
                                    ->addMediaFromDisk($data['document'], 'local')
                                    ->toMediaCollection($key);

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),
                        Action::make('supprimer_checklist_' . $key)
                            ->label('Supprimer')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible(fn (Model $record) => (bool) $getStatut($record)['media'])
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function (Model $record, $livewire) use ($getStatut) {
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
            $getStatut = fn (Model $record) => self::resolveCabinetMediaOwner($record)->getPieceJointeStatut($key);

            $cards[] = Section::make($label)
                ->description('Pièce du cabinet')
                ->icon(fn (Model $record) => $getStatut($record)['media'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (Model $record) => $getStatut($record)['media'] ? 'success' : 'warning')
                ->compact()->collapsible()->collapsed()
                ->schema([
                    TextEntry::make('cabinet_etat_' . $key)
                        ->label('État')
                        ->state(fn (Model $record) => $getStatut($record)['media'] ? 'Déposé' : 'Manquant')
                        ->badge()
                        ->color(fn (Model $record) => $getStatut($record)['media'] ? 'success' : 'danger'),

                    TextEntry::make('cabinet_date_ajout_' . $key)
                        ->label("Date d'ajout")
                        ->state(fn (Model $record) => $getStatut($record)['date_ajout']?->format('d/m/Y') ?? '—'),

                    Actions::make([
                        Action::make('voir_cabinet_' . $key)
                            ->label('Voir')->icon('heroicon-o-eye')->color('info')
                            ->visible(fn (Model $record) => (bool) $getStatut($record)['media'])
                            ->modalHeading($label)
                            ->modalContent(function (Model $record) use ($key) {
                                $media = self::resolveCabinetMediaOwner($record)->getFirstMedia($key);
                                if (! $media) return null;
                                return view('filament.modals.apercu-fichier', ['url' => route('media.stream', $media), 'mime' => $media->mime_type]);
                            })
                            ->modalSubmitAction(false)->modalCancelAction(false)->modalWidth('4xl'),

                        Action::make('telecharger_cabinet_' . $key)
                            ->label('Télécharger')->icon('heroicon-o-arrow-down-tray')->color('success')
                            ->visible(fn (Model $record) => (bool) $getStatut($record)['media'])
                            ->action(function (Model $record) use ($key) {
                                $media = self::resolveCabinetMediaOwner($record)->getFirstMedia($key);
                                if (! $media || ! file_exists($media->getPath())) {
                                    Notification::make()->danger()->title('Fichier introuvable')->send();
                                    return;
                                }
                                return response()->download($media->getPath(), $media->file_name);
                            }),

                        Action::make('gerer_cabinet_' . $key)
                            ->label(fn (Model $record) => $getStatut($record)['media'] ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color(fn (Model $record) => $getStatut($record)['media'] ? 'gray' : 'primary')
                            ->modalHeading('Gestion de la pièce : ' . $label)
                            ->form([
                                FileUpload::make('document')->label('Document (PDF / Image)')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->maxSize(10240)->disk('local')->directory('cabinet-checklist-tmp')->required(),
                            ])
                            ->action(function (array $data, Model $record, $livewire) use ($key, $label) {
                                self::resolveCabinetMediaOwner($record)
                                    ->addMediaFromDisk($data['document'], 'local')
                                    ->toMediaCollection($key);

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),

                        Action::make('supprimer_cabinet_' . $key)
                            ->label('Supprimer')->icon('heroicon-o-trash')->color('danger')
                            ->visible(fn (Model $record) => (bool) $getStatut($record)['media'])
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function (Model $record, $livewire) use ($key) {
                                $media = self::resolveCabinetMediaOwner($record)->getFirstMedia($key);
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

    /**
     * Pièces "Documents à joindre" qui restent au niveau de l'entreprise
     * (CIN du gérant, logo) : stockées directement sur EntrepriseCliente,
     * partagées par toutes les formations de cette entreprise. Utilisé
     * uniquement sur l'Archive.
     */
    protected static function piecesAJoindreEntrepriseCards(): array
    {
        $cards = [];
        $pieces = collect(EntrepriseCliente::PIECES_JOINTES)->only(['cin_gerant', 'logo']);

        foreach ($pieces as $key => $label) {
            $hasMedia = fn (Model $record) => (bool) $record->entrepriseCliente?->hasMedia($key);

            $cards[] = Section::make($label)
                ->icon(fn (Model $record) => $hasMedia($record) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (Model $record) => $hasMedia($record) ? 'success' : 'warning')
                ->description(fn (Model $record) => $hasMedia($record) ? 'Déposé' : 'Manquant')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    Actions::make([
                        Action::make('voir_entreprise_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible($hasMedia)
                            ->modalHeading($label)
                            ->modalContent(function (Model $record) use ($key) {
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

                        Action::make('telecharger_entreprise_' . $key)
                            ->label('Télécharger')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->visible($hasMedia)
                            ->url(fn (Model $record) => route('media.stream', $record->entrepriseCliente?->getFirstMedia($key)))
                            ->openUrlInNewTab(),

                        Action::make('gerer_entreprise_' . $key)
                            ->label(fn (Model $record) => $hasMedia($record) ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color(fn (Model $record) => $hasMedia($record) ? 'gray' : 'primary')
                            ->modalHeading('Gestion de la pièce : ' . $label)
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
                            ->action(function (Model $record, array $data, $livewire) use ($key, $label) {
                                $entreprise = $record->entrepriseCliente;
                                if (! $entreprise) {
                                    return;
                                }

                                $entreprise
                                    ->addMediaFromDisk($data['fichier_' . $key], 'local')
                                    ->toMediaCollection($key);

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),

                        Action::make('supprimer_entreprise_' . $key)
                            ->label('Supprimer')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible($hasMedia)
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function (Model $record, $livewire) use ($key) {
                                $media = $record->entrepriseCliente?->getFirstMedia($key);
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

    /**
     * Pièces "Documents à joindre" propres à chaque formation (éligibilité
     * CSF, facture pro forma) : stockées sur le DossierGiac de la
     * formation, isolées entre formations d'une même entreprise. Utilisé
     * uniquement sur la page "Documents à joindre" d'une Formation.
     */
    protected static function piecesAJoindreFormationCards(): array
    {
        $cards = [];
        
        $pieces = [
            'facture_pro_forma' => 'Facture pro forma (originale)',
        ];

        foreach ($pieces as $key => $label) {
            $hasMedia = fn (Model $record) => (bool) self::resolveMediaOwner($record)?->hasMedia($key);

            $cards[] = Section::make($label)
                ->icon(fn (Model $record) => $hasMedia($record) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (Model $record) => $hasMedia($record) ? 'success' : 'warning')
                ->description(fn (Model $record) => $hasMedia($record) ? 'Déposé' : 'Manquant')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    Actions::make([
                        Action::make('voir_formation_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible($hasMedia)
                            ->modalHeading($label)
                            ->modalContent(function (Model $record) use ($key) {
                                $media = self::resolveMediaOwner($record)?->getFirstMedia($key);
                                if (! $media) return null;

                                return view('filament.modals.apercu-fichier', [
                                    'url' => route('media.stream', $media),
                                    'mime' => $media->mime_type,
                                ]);
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalWidth('4xl'),

                        Action::make('telecharger_formation_' . $key)
                            ->label('Télécharger')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->visible($hasMedia)
                            ->url(fn (Model $record) => route('media.stream', self::resolveMediaOwner($record)?->getFirstMedia($key)))
                            ->openUrlInNewTab(),

                        Action::make('gerer_formation_' . $key)
                            ->label(fn (Model $record) => $hasMedia($record) ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color(fn (Model $record) => $hasMedia($record) ? 'gray' : 'primary')
                            ->modalHeading('Gestion de la pièce : ' . $label)
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
                            ->action(function (Model $record, array $data, $livewire) use ($key, $label) {
                                $dossier = self::resolveMediaOwner($record);
                                if (! $dossier) {
                                    return;
                                }

                                $dossier
                                    ->addMediaFromDisk($data['fichier_' . $key], 'local')
                                    ->toMediaCollection($key);

                                Notification::make()->success()->title("Pièce '{$label}' enregistrée avec succès")->send();
                                $livewire->dispatch('$refresh');
                            }),

                        Action::make('supprimer_formation_' . $key)
                            ->label('Supprimer')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible($hasMedia)
                            ->requiresConfirmation()
                            ->modalHeading('Supprimer la pièce : ' . $label)
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer ce document ?')
                            ->action(function (Model $record, $livewire) use ($key) {
                                $media = self::resolveMediaOwner($record)?->getFirstMedia($key);
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
            $hasMedia = fn (Model $record) => (bool) self::resolveMediaOwner($record)?->hasMedia($key);

            $cards[] = Section::make($label)
                ->icon(fn (Model $record) => $hasMedia($record) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->iconColor(fn (Model $record) => $hasMedia($record) ? 'success' : 'warning')
                ->description(fn (Model $record) => $hasMedia($record) ? 'Déposé' : 'Manquant')
                ->compact()
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('date_ajout_' . $key)
                        ->label("Date d'ajout")
                        ->state(fn (Model $record) =>self::resolveMediaOwner($record)?->getFirstMedia($key)?->created_at)
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),

                    Actions::make([
                        Action::make('voir_ofppt_' . $key)
                            ->label('Voir')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible($hasMedia)
                            ->modalHeading($label)
                            ->modalContent(function (Model $record) use ($key) {
                                $media = self::resolveMediaOwner($record)?->getFirstMedia($key);
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
                            ->url(fn (Model $record) => route('media.stream', self::resolveMediaOwner($record)?->getFirstMedia($key)))
                            ->openUrlInNewTab(),

                        Action::make('gerer_ofppt_' . $key)
                            ->label(fn (Model $record) => $hasMedia($record) ? 'Remplacer' : 'Téléverser')
                            ->icon('heroicon-o-arrow-up-tray')
                            ->color(fn (Model $record) => $hasMedia($record) ? 'gray' : 'primary')
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
                            ->action(function (Model $record, array $data, $livewire) use ($key, $label) {
                                $dossier =self::resolveMediaOwner($record);
                                if (! $dossier) return;

                                $dossier
                                    ->addMediaFromDisk($data['fichier_' . $key], 'local')
                                    ->toMediaCollection($key);

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
                            ->action(function (Model $record, $livewire) use ($key) {
                                $media = self::resolveMediaOwner($record)?->getFirstMedia($key);
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
}
