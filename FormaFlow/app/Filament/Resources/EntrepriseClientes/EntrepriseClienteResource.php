<?php

namespace App\Filament\Resources\EntrepriseClientes;

use App\Filament\Resources\EntrepriseClientes\Pages\CreateEntrepriseCliente;
use App\Filament\Resources\EntrepriseClientes\Pages\EditEntrepriseCliente;
use App\Filament\Resources\EntrepriseClientes\Pages\ListEntrepriseClientes;
use App\Filament\Resources\EntrepriseClientes\Pages\ViewEntrepriseCliente;
use App\Filament\Resources\EntrepriseClientes\Schemas\EntrepriseClienteForm;
use App\Filament\Resources\EntrepriseClientes\Schemas\EntrepriseClienteInfolist;
use App\Filament\Resources\EntrepriseClientes\Tables\EntrepriseClientesTable;
use App\Models\EntrepriseCliente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EntrepriseClienteResource extends Resource
{
    protected static ?string $model = EntrepriseCliente::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'raison_sociale';
    protected static string|null|\UnitEnum $navigationGroup = 'Gestion des Entreprises';

    public static function form(Schema $schema): Schema
    {
        return EntrepriseClienteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EntrepriseClienteInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['raison_sociale', 'ice', 'email', 'gerant.nom', 'gerant.prenom'];
    }

    public static function table(Table $table): Table
    {
        return EntrepriseClientesTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('gerant');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FormationsRelationManager::class,
            RelationManagers\ParticipantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntrepriseClientes::route('/'),
            'create' => CreateEntrepriseCliente::route('/create'),
            'view' => ViewEntrepriseCliente::route('/{record}'),
            'edit' => EditEntrepriseCliente::route('/{record}/edit'),
        ];
    }
}
