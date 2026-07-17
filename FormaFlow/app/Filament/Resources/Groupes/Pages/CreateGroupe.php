<?php

namespace App\Filament\Resources\Groupes\Pages;

use App\Filament\Resources\Groupes\GroupeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateGroupe extends CreateRecord
{
    protected static string $resource = GroupeResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Créer le Groupe');
    }
}
