<?php

use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Gerant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// A valid payload matching every 'required' rule in StoreEntrepriseClienteRequest.
// Kept as a function (not a beforeEach variable) so each test can override just
// the field it cares about via array_merge, without repeating all the others.
function payloadEntrepriseValide(array $overrides = []): array
{
    return array_merge([
        'raison_sociale' => 'Atlas Formations SARL',
        'siege_social' => '12 Avenue Hassan II, Rabat',
        'ville' => 'Rabat',
        'ice' => '001234567000012', // exactly 15 chars, required by 'size:15'
        'if' => '12345678',
        'secteur_activite' => 'Informatique',
        'email' => 'contact@atlas-formations.ma',
        'gerant_nom' => 'Alaoui',
        'gerant_prenom' => 'Youssef',
        'gerant_fonction' => 'Directeur Général',
        'gerant_cin' => 'AB123456',
        'gerant_email' => 'gerant@atlas-formations.ma',
        'gerant_genre' => 'Homme',
    ], $overrides);
}

// --- index -----------------------------------------------------------------

it('lists entreprises with pagination metadata', function () {
    EntrepriseCliente::factory()->count(3)->create();

    $response = $this->getJson('/api/entreprise-clientes');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('meta.total', 3);
});

// --- show --------------------------------------------------------------------

it('shows a single entreprise with its gerant loaded', function () {
    $gerant = Gerant::factory()->create();
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => $gerant->id]);

    $response = $this->getJson("/api/entreprise-clientes/{$entreprise->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $entreprise->id)
        ->assertJsonPath('data.gerant.id', $gerant->id);
});

it('returns a 404 for an entreprise that does not exist', function () {
    $response = $this->getJson('/api/entreprise-clientes/999999');

    $response->assertNotFound();
});

// --- store -------------------------------------------------------------------

it('creates an entreprise and its gerant together from a valid payload', function () {
    $response = $this->postJson('/api/entreprise-clientes', payloadEntrepriseValide());

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.raison_sociale', 'Atlas Formations SARL')
        ->assertJsonPath('data.gerant.nom', 'Alaoui');

    $this->assertDatabaseHas('entreprise_clientes', ['raison_sociale' => 'Atlas Formations SARL']);
    $this->assertDatabaseHas('gerants', ['cin' => 'AB123456']);
});

it('rejects creation when a required field is missing', function () {
    $response = $this->postJson('/api/entreprise-clientes', payloadEntrepriseValide(['raison_sociale' => '']));

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors('raison_sociale');
});

it('rejects creation when the ice is not exactly 15 characters', function () {
    $response = $this->postJson('/api/entreprise-clientes', payloadEntrepriseValide(['ice' => '123']));

    $response->assertStatus(422)->assertJsonValidationErrors('ice');
});

it('rejects creation when the ice is already used by another entreprise', function () {
    EntrepriseCliente::factory()->create(['ice' => '001234567000012']);

    $response = $this->postJson('/api/entreprise-clientes', payloadEntrepriseValide());

    $response->assertStatus(422)->assertJsonValidationErrors('ice');
});

it('rejects creation when gerant_genre is not a valid GerantGender value', function () {
    $response = $this->postJson('/api/entreprise-clientes', payloadEntrepriseValide(['gerant_genre' => 'Autre']));

    $response->assertStatus(422)->assertJsonValidationErrors('gerant_genre');
});

// --- update ------------------------------------------------------------------

it('updates an entreprise and its gerant with a partial payload', function () {
    $gerant = Gerant::factory()->create(['nom' => 'Ancien']);
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => $gerant->id]);

    $response = $this->putJson("/api/entreprise-clientes/{$entreprise->id}", [
        'raison_sociale' => 'Nouvelle Raison Sociale',
        'gerant_nom' => 'Nouveau',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.raison_sociale', 'Nouvelle Raison Sociale')
        ->assertJsonPath('data.gerant.nom', 'Nouveau');

    // sanity check: fields NOT sent in the payload should be untouched
    expect($entreprise->fresh()->siege_social)->toBe($entreprise->siege_social);
});

it('rejects update when the new email collides with a different entreprise', function () {
    EntrepriseCliente::factory()->create(['email' => 'taken@example.com']);
    $entreprise = EntrepriseCliente::factory()->create();

    $response = $this->putJson("/api/entreprise-clientes/{$entreprise->id}", [
        'email' => 'taken@example.com',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('email');
});

// --- destroy -------------------------------------------------------------------

it('deletes an entreprise that has no formations or participants', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    $response = $this->deleteJson("/api/entreprise-clientes/{$entreprise->id}");

    $response->assertOk()->assertJsonPath('success', true);
    $this->assertDatabaseMissing('entreprise_clientes', ['id' => $entreprise->id]);
});

// This is the interesting one: it proves the controller correctly translates
// the model's SuppressionBloqueeException into a 422 JSON response, rather
// than letting it bubble up into a raw 500 error page.
it('returns 422 with a clear message when trying to delete an entreprise that has formations', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Formation::factory()->create(['entreprise_id' => $entreprise->id]);

    $response = $this->deleteJson("/api/entreprise-clientes/{$entreprise->id}");

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonStructure(['message']);

    $this->assertDatabaseHas('entreprise_clientes', ['id' => $entreprise->id]);
});
