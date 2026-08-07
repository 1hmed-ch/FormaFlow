<?php

use App\Filament\Resources\Groupes\Pages\CreateGroupe;
use App\Filament\Resources\Groupes\Pages\EditGroupe;
use App\Filament\Resources\Groupes\Pages\ListGroupes;
use App\Models\Groupe;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

//1. LISTE

it('affiche la page liste des groupes sans erreur', function () {
    Livewire::test(ListGroupes::class)
        ->assertSuccessful();
});

it('affiche les groupes existants dans la table', function () {
    $groupes = Groupe::factory()->count(3)->create();

    Livewire::test(ListGroupes::class)
        ->assertCanSeeTableRecords($groupes);
});

//2. CRÉATION

it('crée un groupe avec des données valides', function () {
    $theme = Theme::factory()->create();

    Livewire::test(CreateGroupe::class)
        ->fillForm([
            'libelle' => 'Groupe A - Session Matin',
            'effectif_max' => 20,
            'theme_id' => $theme->id,
            'lieu' => 'Salle de conférence principale',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('groupes', [
        'libelle' => 'Groupe A - Session Matin',
        'theme_id' => $theme->id,
        'effectif_max' => 20,
    ]);
});

it('rejette la création si les champs obligatoires sont manquants', function () {
    Livewire::test(CreateGroupe::class)
        ->fillForm([
            'libelle' => '',
            'effectif_max' => null,
            'theme_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'libelle' => 'required',
            'effectif_max' => 'required',
            'theme_id' => 'required',
        ]);
});

it('rejette la création si l\'effectif maximal est inférieur à 1', function () {
    $theme = Theme::factory()->create();

    Livewire::test(CreateGroupe::class)
        ->fillForm([
            'libelle' => 'Groupe invalide',
            'effectif_max' => 0,
            'theme_id' => $theme->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['effectif_max']);
});

//3. MODIFICATION

it('modifie un groupe existant', function () {
    $groupe = Groupe::factory()->create(['libelle' => 'Ancien Libellé']);
    $nouveauTheme = Theme::factory()->create();

    Livewire::test(EditGroupe::class, ['record' => $groupe->id])
        ->fillForm([
            'libelle' => 'Nouveau Libellé',
            'theme_id' => $nouveauTheme->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('groupes', [
        'id' => $groupe->id,
        'libelle' => 'Nouveau Libellé',
        'theme_id' => $nouveauTheme->id,
    ]);
});

//4. SUPPRESSION

it('supprime un groupe depuis la table de liste', function () {
    $groupe = Groupe::factory()->create();

    Livewire::test(ListGroupes::class)
        ->callTableAction('delete', record: $groupe);

    $this->assertDatabaseMissing('groupes', ['id' => $groupe->id]);
});