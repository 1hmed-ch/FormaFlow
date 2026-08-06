<?php

use App\Enums\DemandeFinancementStatus;
use App\Exceptions\SuppressionBloqueeException;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Gerant;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

// This is the key line that separates "Unit" tests from "Feature" tests:
// RefreshDatabase migrates a fresh schema before the suite runs, then wraps
// EVERY test in a DB transaction that's rolled back at the end — so tests
// never see each other's leftover data, and your real dev DB is never touched.
uses(RefreshDatabase::class);

it('persists to the database via its factory', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    // assertDatabaseHas is a Laravel/Pest helper: it queries the real test
    // DB directly rather than trusting the in-memory PHP object, so it also
    // catches issues like a column not actually being saved.
    expect($entreprise)->toBeInstanceOf(EntrepriseCliente::class);
    $this->assertDatabaseHas('entreprise_clientes', [
        'id' => $entreprise->id,
        'raison_sociale' => $entreprise->raison_sociale,
    ]);
});

it('casts attributes to their expected PHP types', function () {
    $entreprise = EntrepriseCliente::factory()->create([
        'deja_depose_giac' => 1,
        'statut_demande_financement' => DemandeFinancementStatus::EN_COURS,
    ]);

    expect($entreprise->deja_depose_giac)->toBeTrue()
        ->and($entreprise->statut_demande_financement)->toBe(DemandeFinancementStatus::EN_COURS)
        ->and($entreprise->date_creation)->toBeInstanceOf(Illuminate\Support\Carbon::class);
});

it('belongs to a gerant', function () {
    $gerant = Gerant::factory()->create();
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => $gerant->id]);

    expect($entreprise->gerant)->toBeInstanceOf(Gerant::class)
        ->and($entreprise->gerant->id)->toBe($gerant->id);
});

it('has many formations', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Formation::factory()->count(3)->create(['entreprise_id' => $entreprise->id]);

    expect($entreprise->formations)->toHaveCount(3);
});

it('reports a piece jointe as Manquant when nothing has been uploaded', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    expect($entreprise->getPieceJointeStatut('cin_gerant'))->toBe('Manquant');
});

it('blocks deletion when the entreprise still has formations', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Formation::factory()->create(['entreprise_id' => $entreprise->id]);

    // This is testing the static::deleting() hook in EntrepriseCliente::booted().
    // expect(fn () => ...)->toThrow(...) is Pest's way of asserting an
    // exception is thrown, without a manual try/catch block.
    expect(fn () => $entreprise->delete())
        ->toThrow(SuppressionBloqueeException::class);

    // And the record really is still there afterwards:
    $this->assertDatabaseHas('entreprise_clientes', ['id' => $entreprise->id]);
});

it('blocks deletion when the entreprise still has participants', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Participant::create([
        'nom' => 'Test',
        'prenom' => 'Participant',
        'cin' => 'AB123456',
        'categorie_sp' => 'C',
        'entreprise_id' => $entreprise->id,
    ]);

    expect(fn () => $entreprise->delete())
        ->toThrow(SuppressionBloqueeException::class);
});

it('allows deletion when there are no formations or participants', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    $entreprise->delete();

    $this->assertDatabaseMissing('entreprise_clientes', ['id' => $entreprise->id]);
});
