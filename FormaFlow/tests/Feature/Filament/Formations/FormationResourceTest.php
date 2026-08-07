<?php

use App\Enums\FormationStatus;
use App\Enums\TypeFormation;
use App\Exceptions\SuppressionBloqueeException;
use App\Filament\Resources\Formations\Pages\CreateFormation;
use App\Filament\Resources\Formations\Pages\EditFormation;
use App\Filament\Resources\Formations\Pages\ListFormations;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// 1. LISTE

it('affiche la page liste des formations sans erreur', function () {
    Livewire::test(ListFormations::class)
        ->assertSuccessful();
});

it('affiche les formations existantes dans la table', function () {
    $formations = Formation::factory()->count(3)->create();

    Livewire::test(ListFormations::class)
        ->assertCanSeeTableRecords($formations);
});

// 2. CRÉATION

it('crée une formation avec des données valides', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    Livewire::test(CreateFormation::class)
        ->fillForm([
            'intitule' => 'Formation Management Agile',
            'date_debut' => '2026-09-01',
            'date_fin' => '2026-09-10',
            'entreprise_id' => $entreprise->id,
            'type_formation' => TypeFormation::INGENIERIE->value,
            'statut' => FormationStatus::PLANIFIEE->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('formations', [
        'intitule' => 'Formation Management Agile',
        'entreprise_id' => $entreprise->id,
    ]);
});

it('rejette la création si les champs obligatoires sont manquants', function () {
    Livewire::test(CreateFormation::class)
        ->fillForm([
            'intitule' => '',
            'entreprise_id' => null,
            'type_formation' => null,
            'statut' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'intitule' => 'required',
            'entreprise_id' => 'required',
            'type_formation' => 'required',
            'statut' => 'required',
        ]);
});

it('rejette la création si la date de fin précède la date de début', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    Livewire::test(CreateFormation::class)
        ->fillForm([
            'intitule' => 'Formation avec dates invalides',
            'date_debut' => '2026-09-10',
            'date_fin' => '2026-09-01',
            'entreprise_id' => $entreprise->id,
            'type_formation' => TypeFormation::DIAGNOSTIC->value,
            'statut' => FormationStatus::PLANIFIEE->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['date_fin']);
});

//3. MODIFICATION

it('modifie une formation existante', function () {
    $formation = Formation::factory()->create(['intitule' => 'Ancien intitulé']);

    Livewire::test(EditFormation::class, ['record' => $formation->id])
        ->fillForm([
            'intitule' => 'Nouvel intitulé',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('formations', [
        'id' => $formation->id,
        'intitule' => 'Nouvel intitulé',
    ]);
});

//4. SUPPRESSION

it('supprime une formation depuis la table de liste quand aucun thème n\'a de groupe', function () {
    $formation = Formation::factory()->create();
    Theme::factory()->create(['formation_id' => $formation->id]); // sans groupe

    Livewire::test(ListFormations::class)
        ->callTableAction('delete', record: $formation);

    $this->assertDatabaseMissing('formations', ['id' => $formation->id]);
});

it('bloque la suppression depuis le panel quand un thème a un groupe actif (même garde-fou que l\'API)', function () {
    $formation = Formation::factory()->create();
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);
    Groupe::factory()->create(['theme_id' => $theme->id]);

    expect(fn () => Livewire::test(ListFormations::class)
        ->callTableAction('delete', record: $formation))
        ->toThrow(SuppressionBloqueeException::class);

    $this->assertDatabaseHas('formations', ['id' => $formation->id]);
});