<?php

use App\Enums\FormateurStatus;
use App\Models\EntrepriseFormation;
use App\Models\Formateur;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('casts statut to the FormateurStatus enum', function () {
    $formateur = Formateur::factory()->create();

    expect($formateur->statut)->toBeInstanceOf(FormateurStatus::class);
});

// full_name is a computed accessor (Attribute::make), not a real column —
// this test protects the "{nom} {prenom}" concatenation logic specifically.
it('computes full_name from nom and prenom', function () {
    $formateur = Formateur::factory()->create([
        'nom' => 'Alaoui',
        'prenom' => 'Youssef',
    ]);

    expect($formateur->full_name)->toBe('Alaoui Youssef');
});

it('belongs to an organisme (EntrepriseFormation)', function () {
    $organisme = EntrepriseFormation::factory()->create();
    $formateur = Formateur::factory()->create(['entreprise_formation_id' => $organisme->id]);

    expect($formateur->organisme->id)->toBe($organisme->id);
});

it('has many themes', function () {
    $formateur = Formateur::factory()->create();
    Theme::factory()->count(2)->create(['formateur_id' => $formateur->id]);

    expect($formateur->themes)->toHaveCount(2);
});
