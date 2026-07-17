<?php

namespace App\Filament\Resources\Participants\Pages;

use App\Filament\Resources\Participants\ParticipantResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateParticipant extends CreateRecord
{
    protected static string $resource = ParticipantResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Créer le Participant');
    }
}
