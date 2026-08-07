<?php

use App\Enums\FormationStatus;
use App\Exceptions\SuppressionBloqueeException;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('casts statut and type_formation to their enums', function () {
    $formation = Formation::factory()->create();

    expect($formation->statut)->toBeInstanceOf(FormationStatus::class)
        ->and($formation->date_debut)->toBeInstanceOf(Illuminate\Support\Carbon::class);
});

it('belongs to an entreprise cliente', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $formation = Formation::factory()->create(['entreprise_id' => $entreprise->id]);

    expect($formation->entrepriseCliente->id)->toBe($entreprise->id);
});

it('has many themes', function () {
    $formation = Formation::factory()->create();
    Theme::factory()->count(2)->create(['formation_id' => $formation->id]);

    expect($formation->themes)->toHaveCount(2);
});

// --- Scopes ------------------------------------------------------------

it('scopeTerminees only returns formations with statut Terminee', function () {
    Formation::factory()->terminee()->create();
    Formation::factory()->terminee()->create();
    Formation::factory()->create(); // Planifiée, should be excluded

    $result = Formation::terminees()->get();

    expect($result)->toHaveCount(2)
        ->and($result->every(fn (Formation $f) => $f->statut === FormationStatus::TERMINEE))->toBeTrue();
});

it('scopeDansPeriode only returns formations overlapping the given range', function () {
    // Runs fully inside the query window
    $inside = Formation::factory()->create([
        'date_debut' => '2026-03-01',
        'date_fin' => '2026-03-15',
    ]);

    // Ends before the window starts -> excluded
    $before = Formation::factory()->create([
        'date_debut' => '2026-01-01',
        'date_fin' => '2026-01-10',
    ]);

    // Starts after the window ends -> excluded
    $after = Formation::factory()->create([
        'date_debut' => '2026-06-01',
        'date_fin' => '2026-06-10',
    ]);

    $result = Formation::dansPeriode('2026-02-01', '2026-04-01')->get();

    expect($result->pluck('id'))->toContain($inside->id)
        ->and($result->pluck('id'))->not->toContain($before->id)
        ->and($result->pluck('id'))->not->toContain($after->id);
});

it('scopeDansPeriode with no bounds returns everything', function () {
    Formation::factory()->count(3)->create();

    expect(Formation::dansPeriode(null, null)->count())->toBe(3);
});

// --- Deletion guard ------------------------------------------------------

it('blocks deletion when a theme has an active groupe', function () {
    $formation = Formation::factory()->create();
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);
    Groupe::create([
        'libelle' => 'Groupe A',
        'lieu' => 'Rabat',
        'effectif_max' => 20,
        'theme_id' => $theme->id,
    ]);

    expect(fn () => $formation->delete())
        ->toThrow(SuppressionBloqueeException::class);
});

it('allows deletion when no theme has a groupe', function () {
    $formation = Formation::factory()->create();
    Theme::factory()->create(['formation_id' => $formation->id]); // no groupe attached

    $formation->delete();

    $this->assertDatabaseMissing('formations', ['id' => $formation->id]);
});
