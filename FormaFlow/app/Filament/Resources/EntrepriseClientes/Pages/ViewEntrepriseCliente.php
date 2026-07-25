<?php

namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Exceptions\DocumentGenerationException;
use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use App\Filament\Resources\EntrepriseClientes\Widgets\EntrepriseClienteFormationsChart;
use App\Filament\Resources\EntrepriseClientes\Widgets\EntrepriseClienteOverview;
use App\Models\EntrepriseCliente;
use App\Services\DocumentGenerationService;
use App\Services\GiacDocumentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEntrepriseCliente extends ViewRecord
{
    protected static string $resource = EntrepriseClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make(actions: [
                // 1. Modèle 6
                Action::make('genererModele6')
                    ->label('Générer Modèle 6')
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
                                ->generateModele6($record, (int) $data['annee']);

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
                    ->label('Bulletin d\'Adhésion (B1)')
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
                
                // 3. G7 - Bulletin Ré-adhésion
                Action::make('genererG7')
                    ->label('Générer G7 (Bulletin Ré-adhésion)')
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
                            // TODO Sara: vérifier le nom exact de la méthode dans GiacDocumentGenerationService
                            // (generateBulletinReadhesion vs generateG7BulletinReadhesion selon la branche)
                            $document = app(GiacDocumentGenerationService::class)
                                ->generateBulletinReadhesion($record, (int) $data['annee']);

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
                
                // 5. G3 - Fiche Organisme Conseil (GIAC)
                Action::make('genererG3')
                    ->label('Générer G3 (GIAC)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function (EntrepriseCliente $record, Action $action) {
                        try {
                            // TODO Sara: vérifier le nom exact de la méthode dans GiacDocumentGenerationService
                            // (generateFicheOrganismeConseil vs generateG3FicheOrganismeConseil selon la branche)
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

                // 6. F3 - Fiche Identification Organisme (OFPPT)
                Action::make('genererF3')
                    ->label('Générer Formulaire F3 (OFPPT)')
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
                EditAction::make(),
            ])->button(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EntrepriseClienteOverview::class,
            EntrepriseClienteFormationsChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}