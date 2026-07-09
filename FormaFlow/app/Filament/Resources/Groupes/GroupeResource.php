<?php

namespace App\Filament\Resources\Groupes;

use App\Filament\Resources\Groupes\Pages\CreateGroupe;
use App\Filament\Resources\Groupes\Pages\EditGroupe;
use App\Filament\Resources\Groupes\Pages\ListGroupes;
use App\Filament\Resources\Groupes\Schemas\GroupeForm;
use App\Filament\Resources\Groupes\Tables\GroupesTable;
use App\Models\Groupe;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GroupeResource extends Resource
{
    protected static ?string $model = Groupe::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'libelle';

    public static function form(Schema $schema): Schema
    {
        return GroupeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GroupesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGroupes::route('/'),
            'create' => CreateGroupe::route('/create'),
            'edit' => EditGroupe::route('/{record}/edit'),
        ];
    }
}
