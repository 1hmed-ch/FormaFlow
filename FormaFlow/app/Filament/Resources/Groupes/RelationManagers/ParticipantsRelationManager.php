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
use Filament\Tables\Columns\TextColumn;
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
                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable(),

                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable(),

                TextColumn::make('cin')
                    ->label('CIN'),

                TextColumn::make('numero_cnss')
                    ->label('N° CNSS'),

                TextColumn::make('email')
                    ->label('Email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->multiple()
                    ->recordTitle(fn ($record) => "{$record->prenom} {$record->nom} - {$record->fonction_occupee} ({$record->categorie_sp->value})")
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
