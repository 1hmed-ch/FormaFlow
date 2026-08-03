<?php

namespace App\Filament\Resources\Archives;

use App\Filament\Resources\Archives\Pages\ListArchives;
use App\Filament\Resources\Archives\Pages\ViewArchive;
use App\Filament\Resources\Archives\Tables\ArchivesTable;
use App\Models\DossierGiac;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ArchiveResource extends Resource
{
    protected static ?string $model = DossierGiac::class;

    protected static ?string $slug = 'archives';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Archives';

    protected static ?string $modelLabel = 'dossier archivé';

    protected static ?string $pluralModelLabel = 'Archives';

    public static function table(Table $table): Table
    {
        return ArchivesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArchives::route('/'),
            'view'  => ViewArchive::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}