<?php

use App\Enums\FormateurStatus;
use App\Models\EntrepriseFormation;
use App\Models\Formateur;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function payloadFormateurValide(array $overrides = []): array
{
    return array_merge([
        'nom' => 'Idrissi',
        'prenom' => 'Karim',
        'email' => 'karim.idrissi@example.com',
        'statut' => FormateurStatus::INTERNE->value,
    ], $overrides);
}

it('lists formateurs with pagination metadata', function () {
    Formateur::factory()->count(2)->create();

    $this->getJson('/api/formateurs')->assertOk()->assertJsonCount(2, 'data');
});

it('shows a single formateur', function () {
    $formateur = Formateur::factory()->create();

    $this->getJson("/api/formateurs/{$formateur->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $formateur->id);
});

it('creates a formateur from a valid payload', function () {
    // StoreFormateurRequest doesn't validate entreprise_formation_id at all,
    // so it's never in $request->validated() -- the insert relies entirely
    // on the migration's `->default(1)` on that column, which in turn
    // requires an EntrepriseFormation row with id=1 to already exist
    // (the foreign key target). EntrepriseFormation::current() guarantees that.
    EntrepriseFormation::current();

    $response = $this->postJson('/api/formateurs', payloadFormateurValide());

    $response->assertCreated()->assertJsonPath('data.nom', 'Idrissi');
});

it('rejects creation when email is already used by another formateur', function () {
    Formateur::factory()->create(['email' => 'karim.idrissi@example.com']);

    $response = $this->postJson('/api/formateurs', payloadFormateurValide());

    $response->assertStatus(422)->assertJsonValidationErrors('email');
});

it('rejects creation when statut is not a valid FormateurStatus value', function () {
    $response = $this->postJson('/api/formateurs', payloadFormateurValide(['statut' => 'FREELANCE']));

    $response->assertStatus(422)->assertJsonValidationErrors('statut');
});

it('updates a formateur with a partial payload', function () {
    $formateur = Formateur::factory()->create(['nom' => 'Ancien']);

    $this->putJson("/api/formateurs/{$formateur->id}", ['nom' => 'Nouveau'])
        ->assertOk()
        ->assertJsonPath('data.nom', 'Nouveau');
});

it('deletes a formateur', function () {
    $formateur = Formateur::factory()->create();

    $this->deleteJson("/api/formateurs/{$formateur->id}")->assertOk();
    $this->assertDatabaseMissing('formateurs', ['id' => $formateur->id]);
});
