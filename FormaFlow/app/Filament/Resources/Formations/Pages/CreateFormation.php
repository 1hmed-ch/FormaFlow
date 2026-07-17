<?php

namespace App\Filament\Resources\Formations\Pages;

use App\Filament\Resources\Formations\FormationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateFormation extends CreateRecord
{
    protected static string $resource = FormationResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Créer la Formation');
    }
}
