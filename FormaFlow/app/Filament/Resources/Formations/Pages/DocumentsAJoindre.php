<?php

namespace App\Filament\Resources\Formations\Pages;

use App\Filament\Resources\Formations\FormationResource;
use App\Filament\Resources\Formations\Schemas\FormationDocumentsInfolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class DocumentsAJoindre extends ViewRecord
{
    protected static string $resource = FormationResource::class;

    protected static ?string $title = 'Documents à joindre';

    public function infolist(Schema $schema): Schema
    {
        return FormationDocumentsInfolist::configure($schema);
    }

    public function getBreadcrumb(): string
    {
        return 'Documents à joindre';
    }
}
