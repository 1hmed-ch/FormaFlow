<?php

namespace App\Filament\Resources\Archives\Pages;

use App\Filament\Resources\Archives\ArchiveResource;
use App\Filament\Resources\Archives\Schemas\ArchiveInfolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewArchive extends ViewRecord
{
    protected static string $resource = ArchiveResource::class;
    public ?string $archiveDocumentsCategorie = null;
    public ?string $archiveDocumentsFormationId = null;
    public ?string $archiveDocumentsDateDebut = null;
    public ?string $archiveDocumentsDateFin = null;


    public function infolist(Schema $schema): Schema
    {
        return ArchiveInfolist::configure($schema);
    }
}
