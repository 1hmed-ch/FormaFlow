<?php

use App\Filament\Resources\Themes\Pages\CreateTheme;
use App\Filament\Resources\Themes\Pages\EditTheme;
use App\Filament\Resources\Themes\Pages\ListThemes;
use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

//1.LISTE
it('affiche la page liste des thèmes sans erreur', function () {
    Livewire::test(ListThemes::class)
        ->assertSuccessful();
});

it('affiche les thèmes existants dans la table', function () {
    $themes = Theme::factory()->count(3)->create();

    Livewire::test(ListThemes::class)
        ->assertCanSeeTableRecords($themes);
});

//2. CRÉATION

it('crée un thème avec des données valides', function () {
    $formation = Formation::factory()->create();
    $formateur = Formateur::factory()->create();

    Livewire::test(CreateTheme::class)
        ->fillForm([
            'intitule' => 'Architecture Microservices',
            'date_debut' => '2026-09-01',
            'date_fin' => '2026-09-05',
            'objectifs' => 'Maîtriser les bases de la conteneurisation.',
            'formation_id' => $formation->id,
            'formateur_id' => $formateur->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('themes', [
        'intitule' => 'Architecture Microservices',
        'formation_id' => $formation->id,
        'formateur_id' => $formateur->id,
    ]);
});

it('rejette la création si les champs obligatoires sont manquants', function () {
    Livewire::test(CreateTheme::class)
        ->fillForm([
            'intitule' => '',
            'date_debut' => null,
            'date_fin' => null,
            'formation_id' => null,
            'formateur_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'intitule' => 'required',
            'date_debut' => 'required',
            'date_fin' => 'required',
            'formation_id' => 'required',
            'formateur_id' => 'required',
        ]);
});

it('rejette la création si la date de fin précède la date de début', function () {
    $formation = Formation::factory()->create();
    $formateur = Formateur::factory()->create();

    Livewire::test(CreateTheme::class)
        ->fillForm([
            'intitule' => 'Thème avec dates invalides',
            'date_debut' => '2026-09-10',
            'date_fin' => '2026-09-01', // avant date_debut
            'formation_id' => $formation->id,
            'formateur_id' => $formateur->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['date_fin']);
});

//3.MODIFICATION
it('modifie un thème existant', function () {
    $theme = Theme::factory()->create(['intitule' => 'Ancien intitulé']);
    $nouvelleFormation = Formation::factory()->create();

    Livewire::test(EditTheme::class, ['record' => $theme->id])
        ->fillForm([
            'intitule' => 'Nouvel intitulé',
            'formation_id' => $nouvelleFormation->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('themes', [
        'id' => $theme->id,
        'intitule' => 'Nouvel intitulé',
        'formation_id' => $nouvelleFormation->id,
    ]);
});

//4.SUPPRESSION


it('supprime un thème depuis la table de liste', function () {
    $theme = Theme::factory()->create();

    Livewire::test(ListThemes::class)
        ->callTableAction('delete', record: $theme);

    $this->assertDatabaseMissing('themes', ['id' => $theme->id]);
});