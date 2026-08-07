<?php

use App\Filament\Pages\ManageSettings;
use App\Models\EntrepriseFormation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
});

it('affiche la page ManageSettings sans erreur', function () {
    Livewire::test(ManageSettings::class)->assertSuccessful();
});

it("pré-remplit le formulaire avec les données existantes de l'organisme", function () {

    $organisme = EntrepriseFormation::current();
    $organisme->update(['raison_sociale' => 'Cabinet Atlas Formation']);

    Livewire::test(ManageSettings::class)
        ->assertFormSet([
            'raison_sociale' => 'Cabinet Atlas Formation',
        ]);
});

it("sauvegarde les paramètres de l'organisme de formation", function () {
    EntrepriseFormation::current(); // garantit que la fiche id=1 existe avant le test

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'raison_sociale' => 'Nouvelle Raison Sociale',
            'ville' => 'Fès',
        ])
        ->call('save');

    $this->assertDatabaseHas('entreprise_formations', [
        'id' => 1,
        'raison_sociale' => 'Nouvelle Raison Sociale',
        'ville' => 'Fès',
    ]);
});