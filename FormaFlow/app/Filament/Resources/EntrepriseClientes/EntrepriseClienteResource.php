<?php

namespace App\Filament\Resources\EntrepriseClientes;

use App\Filament\Resources\EntrepriseClientes\Pages\CreateEntrepriseCliente;
use App\Filament\Resources\EntrepriseClientes\Pages\EditEntrepriseCliente;
use App\Filament\Resources\EntrepriseClientes\Pages\ListEntrepriseClientes;
use App\Filament\Resources\EntrepriseClientes\Schemas\EntrepriseClienteForm;
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

    public static function form(Schema $schema): Schema
    {
        return EntrepriseClienteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntrepriseClientesTable::configure($table);
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
            'index' => ListEntrepriseClientes::route('/'),
            'create' => CreateEntrepriseCliente::route('/create'),
            'edit' => EditEntrepriseCliente::route('/{record}/edit'),
        ];
    }
}
