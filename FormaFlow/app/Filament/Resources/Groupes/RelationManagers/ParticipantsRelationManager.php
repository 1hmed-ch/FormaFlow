<?php

namespace App\Filament\Resources\Groupes\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';
    protected static ?string $title = 'Participants du Groupe';

    public function table(Table $table): Table
    {
        $groupe = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('nom')
            ->columns([
                Tables\Columns\TextColumn::make('nom')->searchable(),
                Tables\Columns\TextColumn::make('prenom')->searchable(),
                Tables\Columns\TextColumn::make('cin'),
                Tables\Columns\TextColumn::make('numero_cnss')->label("N° CNSS"),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->multiple()
                    // 1. Afficher "Nom Prénom" dans la liste déroulante
                    ->recordTitle(fn ($record) => "{$record->nom} {$record->prenom}")
                    // 2. Permettre la recherche par nom OU par prénom
                    ->recordSelectSearchColumns(['nom', 'prenom'])
                    ->recordSelectOptionsQuery(function (Builder $query) use ($groupe) {
                        return $query
                            ->where('entreprise_id', $groupe->theme->formation->entreprise_id)
                            ->whereDoesntHave('groupes', function ($q) use ($groupe) {
                                $q->where('groupes.theme_id', $groupe->theme_id);
                            });
                    })
                    ->before(function (AttachAction $action, array $data) use ($groupe) {
                        $currentCount = $groupe->participants()->count();
                        $toAddCount = count($data['recordId'] ?? []);

                        if (($currentCount + $toAddCount) > $groupe->effectif_max) {
                            $placesRestantes = max(0, $groupe->effectif_max - $currentCount);

                            Notification::make()
                                ->danger()
                                ->title('Capacité maximale dépassée')
                                ->body("Ce groupe ne peut accepter que {$placesRestantes} participant(s) supplémentaire(s).")
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
