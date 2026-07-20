<?php

namespace App\Filament\Resources\EntrepriseClientes\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class EntrepriseClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->description('Identité et activités principales de l\'entreprise')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('raison_sociale')
                            ->label('Raison Sociale')
                            ->weight(FontWeight::Bold)
                            ->columnSpan(2),

                        TextEntry::make('statut_juridique')
                            ->label('Statut Juridique')
                            ->badge(),

                        TextEntry::make('siege_social')
                            ->label('Siège Social')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),

                        TextEntry::make('date_creation')
                            ->label('Date de Création')
                            ->date('d/m/Y')
                            ->placeholder('—'),

                        TextEntry::make('effectif_total')
                            ->label('Effectif Total')
                            ->placeholder('—'),

                        TextEntry::make('secteur_activite')
                            ->label('Secteur d\'Activité'),

                        TextEntry::make('ice')
                            ->label('ICE')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('if')
                            ->label('Identifiant Fiscal (IF)')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('rc')
                            ->label('Registre de Commerce (RC)')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('patente')
                            ->label('Patente')
                            ->placeholder('—'),

                        TextEntry::make('num_cnss')
                            ->label('N° CNSS')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('region_affiliation_cnss')
                            ->label('Région d\'affiliation CNSS')
                            ->placeholder('—'),
                        TextEntry::make('activite')
                            ->label('Activité (Description détaillée)')
                            ->prose()
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Gérant')
                    ->description('Représentant légal de l\'entreprise')
                    ->icon('heroicon-o-user')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('gerant.nom')
                            ->label('Nom')
                            ->placeholder('—'),

                        TextEntry::make('gerant.prenom')
                            ->label('Prénom')
                            ->placeholder('—'),

                        TextEntry::make('gerant.genre')
                            ->label('Genre')
                            ->badge()
                            ->placeholder('—'),

                        TextEntry::make('gerant.cin')
                            ->label('CIN')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('gerant.fonction')
                            ->label('Fonction')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ])->columnSpanFull(),

                Section::make('Coordonnées & Contact')
                    ->description('Informations pour joindre le référent de l\'entreprise')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('contact_ref')
                            ->label('Nom du contact référent')
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make('email')
                            ->label('Adresse Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->columnSpanFull(),

                        TextEntry::make('telephone')
                            ->label('Téléphone')
                            ->icon('heroicon-o-phone')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('fax')
                            ->label('Fax')
                            ->placeholder('—'),
                    ])->columnSpanFull(),

                Section::make('Visuels pour les documents générés')
                    ->description('Utilisées pour habiller le Modèle 6 et la Fiche de présence générés pour cette entreprise')
                    ->icon('heroicon-o-photo')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        ImageEntry::make('image_entete')
                            ->label("Image d'en-tête")
                            ->state(fn ($record) => $record->getEnteteImageBase64())
                            ->height(100)
                            ->placeholder('Aucune image fournie'),

                        ImageEntry::make('image_pied_page')
                            ->label('Image de pied de page')
                            ->state(fn ($record) => $record->getPiedPageImageBase64())
                            ->height(100)
                            ->placeholder('Aucune image fournie'),
                    ]),
            ]);
    }
}
