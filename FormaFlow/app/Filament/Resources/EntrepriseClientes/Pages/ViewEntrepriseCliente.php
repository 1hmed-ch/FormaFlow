<?php
/*
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
use Filament\Support\Icons\Heroicon;

class ViewEntrepriseCliente extends ViewRecord
{
    protected static string $resource = EntrepriseClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make(actions: [
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

                Action::make('genererG3')
                    ->label('Générer G3 (GIAC)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function (EntrepriseCliente $record, Action $action) {
                        try {
                            $document = app(GiacDocumentGenerationService::class)
                                ->generateG3FicheOrganismeConseil($record);

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
                    ->label('Générer Formulaire F3')
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
                                ->generateG7BulletinReadhesion($record, (int) $data['annee']);

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
            ])->button()
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
}*/


namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Exceptions\DocumentGenerationException;
use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use App\Filament\Resources\EntrepriseClientes\Widgets\EntrepriseClienteFormationsChart;
use App\Filament\Resources\EntrepriseClientes\Widgets\EntrepriseClienteOverview;
use App\Models\EntrepriseCliente;
use App\Services\DocumentGenerationService;
use App\Services\GiacDocumentGenerationService;
use Filament\Actions\Action;
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

            Action::make('genererG3')
                ->label('Générer G3 (GIAC)')
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

            EditAction::make(),
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

