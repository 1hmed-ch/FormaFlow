<?php

namespace App\Filament\Resources\Themes\Pages;

use App\Filament\Resources\Themes\ThemeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTheme extends CreateRecord
{
    protected static string $resource = ThemeResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Créer le Thème');
    }
}
