<?php

use App\Enums\FormationStatus;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Formateur;
use App\Models\Gerant;
use App\Models\Groupe;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

// --- index / show ------------------------------------------------------------

it('lists formations with pagination metadata', function () {
    Formation::factory()->count(3)->create();

    $this->getJson('/api/formations')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'data');
});

it('shows a single formation with its entreprise loaded', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $formation = Formation::factory()->create(['entreprise_id' => $entreprise->id]);

    $this->getJson("/api/formations/{$formation->id}")
        ->assertOk()
        ->assertJsonPath('data.entreprise_cliente.id', $entreprise->id);
});

it('returns a 404 for a formation that does not exist', function () {
    $this->getJson('/api/formations/999999')->assertNotFound();
});

// --- store -------------------------------------------------------------------

it('creates a formation from a valid payload', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    $response = $this->postJson('/api/formations', [
        'intitule' => 'Formation en gestion de projet',
        'statut' => FormationStatus::PLANIFIEE->value,
        'entreprise_id' => $entreprise->id,
    ]);

    $response->assertCreated()->assertJsonPath('data.intitule', 'Formation en gestion de projet');
    $this->assertDatabaseHas('formations', ['intitule' => 'Formation en gestion de projet']);
});

it('rejects creation when entreprise_id does not point to a real entreprise', function () {
    $response = $this->postJson('/api/formations', [
        'intitule' => 'Formation X',
        'statut' => FormationStatus::PLANIFIEE->value,
        'entreprise_id' => 999999,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('entreprise_id');
});

it('rejects creation when statut is not a valid FormationStatus value', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    $response = $this->postJson('/api/formations', [
        'intitule' => 'Formation X',
        'statut' => 'not-a-real-status',
        'entreprise_id' => $entreprise->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('statut');
});

// --- update ------------------------------------------------------------------

it('updates a formation with a partial payload', function () {
    $formation = Formation::factory()->create(['intitule' => 'Ancien intitule']);

    $response = $this->putJson("/api/formations/{$formation->id}", [
        'intitule' => 'Nouvel intitule',
    ]);

    $response->assertOk()->assertJsonPath('data.intitule', 'Nouvel intitule');
});

// --- destroy -------------------------------------------------------------------

it('deletes a formation with no groupes attached', function () {
    $formation = Formation::factory()->create();

    $this->deleteJson("/api/formations/{$formation->id}")
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('formations', ['id' => $formation->id]);
});

it('returns 422 when trying to delete a formation whose theme has a groupe', function () {
    $formation = Formation::factory()->create();
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);
    Groupe::factory()->create(['theme_id' => $theme->id]);

    $this->deleteJson("/api/formations/{$formation->id}")
        ->assertStatus(422)
        ->assertJsonPath('success', false);

    $this->assertDatabaseHas('formations', ['id' => $formation->id]);
});

// --- genererModele6 ------------------------------------------------------------

it('downloads Modele 6 as a real PDF when every business rule is satisfied', function () {
    $gerant = Gerant::factory()->create();
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => $gerant->id]);
    $formation = Formation::factory()->terminee()->create(['entreprise_id' => $entreprise->id]);
    Theme::factory()->create(['formation_id' => $formation->id]);

    $annee = now()->year;

    $response = $this->getJson("/api/formations/{$formation->id}/documents/modele-6?annee={$annee}");

    $expectedFilename = sprintf(
        'modele6_%s_%s_%d.pdf',
        Str::slug($entreprise->raison_sociale),
        Str::slug($formation->intitule),
        $annee
    );

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'attachment; filename="' . $expectedFilename . '"');

    expect($response->getContent())->toStartWith('%PDF');
});

it('rejects the Modele 6 request when annee is missing from the query string', function () {
    $formation = Formation::factory()->terminee()->create();

    $this->getJson("/api/formations/{$formation->id}/documents/modele-6")
        ->assertStatus(422)
        ->assertJsonValidationErrors('annee');
});

it('translates a DocumentGenerationException into a 422 JSON response, not a 500', function () {
    // Formation is not Terminee -- generateModele6() should throw
    // DocumentGenerationException, which the controller must catch.
    $formation = Formation::factory()->create();

    $response = $this->getJson("/api/formations/{$formation->id}/documents/modele-6?annee=2026");

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonStructure(['message']);
});
