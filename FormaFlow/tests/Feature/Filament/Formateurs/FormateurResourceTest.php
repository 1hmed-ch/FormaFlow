<?php

use App\Enums\FormateurStatus;
use App\Filament\Resources\Formateurs\Pages\CreateFormateur;
use App\Filament\Resources\Formateurs\Pages\EditFormateur;
use App\Filament\Resources\Formateurs\Pages\ListFormateurs;
use App\Models\Formateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| 1. LISTE
|--------------------------------------------------------------------------
*/

it('affiche la page liste des formateurs sans erreur', function () {
    Livewire::test(ListFormateurs::class)
        ->assertSuccessful();
});

it('affiche les formateurs existants dans la table', function () {
    $formateurs = Formateur::factory()->count(3)->create();

    Livewire::test(ListFormateurs::class)
        ->assertCanSeeTableRecords($formateurs);
});

// 2. CRÉATION

it('crée un formateur avec des données valides', function () {
    Livewire::test(CreateFormateur::class)
        ->fillForm([
            'nom' => 'Bennani',
            'prenom' => 'Yassine',
            'specialite' => 'Développement Web',
            'statut' => FormateurStatus::INTERNE->value,
            'email' => 'yassine.bennani@example.com',
            'telephone' => '+212600000000',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('formateurs', [
        'nom' => 'Bennani',
        'prenom' => 'Yassine',
        'email' => 'yassine.bennani@example.com',
    ]);
});

it('rejette la création si les champs obligatoires sont manquants', function () {
    Livewire::test(CreateFormateur::class)
        ->fillForm([
            'nom' => '',
            'prenom' => '',
            'email' => '',
            'statut' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required',
            'statut' => 'required',
        ]);
});

it('rejette la création si l\'email est déjà utilisé par un autre formateur', function () {
    Formateur::factory()->create(['email' => 'existe.deja@example.com']);

    Livewire::test(CreateFormateur::class)
        ->fillForm([
            'nom' => 'Alami',
            'prenom' => 'Sara',
            'statut' => FormateurStatus::EXTERNE->value,
            'email' => 'existe.deja@example.com',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});

// 3. MODIFICATION

it('modifie un formateur existant', function () {
    $formateur = Formateur::factory()->create(['nom' => 'Ancien Nom']);

    Livewire::test(EditFormateur::class, ['record' => $formateur->id])
        ->fillForm([
            'nom' => 'Nouveau Nom',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('formateurs', [
        'id' => $formateur->id,
        'nom' => 'Nouveau Nom',
    ]);
});

it('permet de garder son propre email en modifiant un formateur (règle unique ignoreRecord)', function () {
    $formateur = Formateur::factory()->create(['email' => 'moi@example.com']);

    Livewire::test(EditFormateur::class, ['record' => $formateur->id])
        ->fillForm([
            'email' => 'moi@example.com', // inchangé, ne doit pas déclencher la règle unique
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

//4. SUPPRESSION

it('supprime un formateur depuis la table de liste', function () {
    $formateur = Formateur::factory()->create();

    Livewire::test(ListFormateurs::class)
        ->callTableAction('delete', record: $formateur);

    $this->assertDatabaseMissing('formateurs', ['id' => $formateur->id]);
});