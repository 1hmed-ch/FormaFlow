<?php

use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('casts date_debut and date_fin to dates', function () {
    $theme = Theme::factory()->create();

    expect($theme->date_debut)->toBeInstanceOf(Illuminate\Support\Carbon::class);
});

it('belongs to a formation', function () {
    $formation = Formation::factory()->create();
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);

    expect($theme->formation->id)->toBe($formation->id);
});

it('belongs to a formateur', function () {
    $formateur = Formateur::factory()->create();
    $theme = Theme::factory()->create(['formateur_id' => $formateur->id]);

    expect($theme->formateur->id)->toBe($formateur->id);
});

it('has many groupes', function () {
    $theme = Theme::factory()->create();
    Groupe::factory()->count(2)->create(['theme_id' => $theme->id]);

    expect($theme->groupes)->toHaveCount(2);
});
