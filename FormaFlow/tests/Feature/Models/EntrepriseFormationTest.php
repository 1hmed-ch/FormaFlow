<?php

use App\Models\EntrepriseFormation;
use App\Models\Formateur;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('casts JSON and boolean attributes correctly', function () {
    $organisme = EntrepriseFormation::factory()->create([
        'domaines_competence' => ['Informatique', 'RH'],
        'appartient_groupe_etranger' => true,
    ]);

    expect($organisme->domaines_competence)->toBe(['Informatique', 'RH'])
        ->and($organisme->appartient_groupe_etranger)->toBeTrue();
});

// getEffectifTotalEtrangersAttribute is a computed, non-stored value —
// this test locks in the sum formula itself, independent of any DB column.
it('sums the four foreign-staff fields for effectif_total_etrangers', function () {
    $organisme = EntrepriseFormation::factory()->create([
        'nb_experts_permanents_etrangers' => 2,
        'nb_experts_vacataires_etrangers' => 1,
        'nb_animateurs_formateurs_etrangers' => 3,
        'nb_autres_employes_etrangers' => 0,
    ]);

    expect($organisme->effectif_total_etrangers)->toBe(6);
});

it('current() returns the same singleton record on every call', function () {
    $first = EntrepriseFormation::current();
    $second = EntrepriseFormation::current();

    expect($first->id)->toBe(1)
        ->and($second->id)->toBe($first->id)
        ->and(EntrepriseFormation::count())->toBe(1);
});

it('has many formateurs', function () {
    $organisme = EntrepriseFormation::factory()->create();
    Formateur::factory()->count(2)->create(['entreprise_formation_id' => $organisme->id]);

    expect($organisme->formateurs)->toHaveCount(2);
});
