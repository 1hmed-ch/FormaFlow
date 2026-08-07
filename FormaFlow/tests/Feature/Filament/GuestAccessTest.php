<?php

use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\Archives\ArchiveResource;
use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use App\Filament\Resources\Formateurs\FormateurResource;
use App\Filament\Resources\Formations\FormationResource;
use App\Filament\Resources\Groupes\GroupeResource;
use App\Filament\Resources\Participants\ParticipantResource;
use App\Filament\Resources\Themes\ThemeResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirige un visiteur non authentifié vers la connexion, sans crash 500', function (string $resourceClass) {
    $response = $this->get($resourceClass::getUrl('index'));

    $response->assertRedirect(); 
})->with([
    EntrepriseClienteResource::class,
    FormationResource::class,
    FormateurResource::class,
    ParticipantResource::class,
    ThemeResource::class,
    GroupeResource::class,
    ArchiveResource::class,
]);

it('redirige un visiteur non authentifié depuis ManageSettings vers la connexion', function () {
    $this->get(ManageSettings::getUrl())->assertRedirect();
});