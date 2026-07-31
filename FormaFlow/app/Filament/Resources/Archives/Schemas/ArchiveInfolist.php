<?php

namespace App\Filament\Resources\Archives\Schemas;

use App\Exceptions\DocumentGenerationException;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use App\Services\DocumentGenerationService;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use App\Services\ArchiveZipService;


class ArchiveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Bloc 1 : Fiche entreprise ──────────────────────
                Section::make('Fiche entreprise')
                    ->description('Fiche d\'information générale de l\'entreprise')
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('entrepriseCliente.raison_sociale')
                            ->label('Entreprise')
                            ->weight(FontWeight::Bold),

                        Actions::make([
                            Action::make('genererFicheEntreprise')
                                ->label('Générer / Télécharger la Fiche entreprise')
                                ->icon('heroicon-o-document-arrow-down')
                                ->color('primary')
                                ->action(function (DossierGiac $record, Action $action) {
                                    try {
                                        $document = app(DocumentGenerationService::class)
                                            ->generateCFicheEntreprise($record->entrepriseCliente);

                                        return response()->streamDownload(
                                            fn () => print($document['content']),
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
                        ]),
                        Actions::make([
                            Action::make('telechargerZip')
                                ->label('Télécharger le dossier complet (ZIP)')
                                ->icon('heroicon-o-archive-box-arrow-down')
                                ->color('success')
                                ->action(function (DossierGiac $record) {
                                    $zipPath = app(ArchiveZipService::class)->build($record);

                                    return response()->download($zipPath)->deleteFileAfterSend(true);
                                }),
                        ])->columnSpanFull(),
                        Action::make('imprimerDossier')
                            ->label('Imprimer le dossier')
                            ->icon('heroicon-o-printer')
                            ->color('gray')
                            ->action(function (DossierGiac $record) {
                                $document = app(DocumentGenerationService::class)
                                    ->generateImpressionDossier($record);

                                return response()->streamDownload(
                                    fn () => print($document['content']),
                                    $document['filename'],
                                    ['Content-Type' => 'application/pdf']
                                );
                            }),
                                            ]),

                // ── Bloc 2 : GIAC Technologies ─────────────────────
                Section::make('GIAC Technologies')
                    ->description('Documents générés par le cabinet pour ce dossier (lecture seule)')
                    ->icon('heroicon-o-building-office')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('checklist_statut')
                            ->label('Statut checklist entreprise')
                            ->state('Non disponible')
                            ->badge()
                            ->color('gray'),

                        RepeatableEntry::make('entrepriseCliente.documentsGeneres')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Type'),
                                TableColumn::make('Catégorie'),
                                TableColumn::make('Statut'),
                                TableColumn::make('Généré le'),
                                TableColumn::make('')->hiddenHeaderLabel(),
                            ])
                            ->schema([
                                TextEntry::make('type_document'),
                                TextEntry::make('categorie')->badge(),
                                TextEntry::make('statut')->badge(),
                                TextEntry::make('genere_le')->dateTime('d/m/Y H:i'),
                                TextEntry::make('nom_fichier')
                                    ->label('')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('primary')
                                    ->url(fn ($record): string => route('documents-generes.telecharger', $record))
                                    ->openUrlInNewTab(),
                            ])
                            ->placeholder('Aucun document GIAC généré pour cette entreprise.'),
                    ]),

                // ── Bloc 3 : Documents à joindre ───────────────────
                Section::make('Documents à joindre')
                    ->description('Pièces fournies par l\'entreprise')
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->collapsible()
                    ->columns(2)
                    ->schema(self::piecesJointesEntries()),
            ]);
    }

    protected static function piecesJointesEntries(): array
    {
        $entries = [];

        foreach (EntrepriseCliente::PIECES_JOINTES as $key => $label) {
            $entries[] = TextEntry::make($key . '_statut')
                ->label($label)
                ->state(fn (DossierGiac $record) => $record->entrepriseCliente?->getPieceJointeStatut($key) ?? 'Manquant')
                ->badge()
                ->color(fn (DossierGiac $record) => $record->entrepriseCliente?->hasMedia($key) ? 'success' : 'danger')
                ->icon(fn (DossierGiac $record) => $record->entrepriseCliente?->hasMedia($key)
                    ? 'heroicon-o-check-circle'
                    : 'heroicon-o-exclamation-triangle')
                ->url(function (DossierGiac $record) use ($key) {
                    $media = $record->entrepriseCliente?->getFirstMedia($key);
                    return $media ? route('media.stream', $media) : null;
                })
                ->openUrlInNewTab();
        }

        return $entries;
    }
}