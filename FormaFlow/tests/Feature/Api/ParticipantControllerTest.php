<?php

use App\Models\EntrepriseCliente;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function payloadParticipantValide(array $overrides = []): array
{
    return array_merge([
        'nom' => 'Bennani',
        'prenom' => 'Sara',
        'cin' => 'AB123456',
        'categorie_sp' => 'C',
        'entreprise_id' => EntrepriseCliente::factory()->create()->id,
    ], $overrides);
}

it('lists participants with pagination metadata', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Participant::create(payloadParticipantValide(['entreprise_id' => $entreprise->id]));
    Participant::create(payloadParticipantValide(['entreprise_id' => $entreprise->id, 'cin' => 'CD999999']));

    $this->getJson('/api/participants')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('shows a single participant with its entreprise loaded', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $participant = Participant::create(payloadParticipantValide(['entreprise_id' => $entreprise->id]));

    $this->getJson("/api/participants/{$participant->id}")
        ->assertOk()
        ->assertJsonPath('data.entreprise_cliente.id', $entreprise->id);
});

it('creates a participant from a valid payload', function () {
    $response = $this->postJson('/api/participants', payloadParticipantValide());

    $response->assertCreated()->assertJsonPath('data.nom', 'Bennani');
    $this->assertDatabaseHas('participants', ['cin' => 'AB123456']);
});

it('rejects creation when cin is already used', function () {
    Participant::create(payloadParticipantValide(['cin' => 'AB123456']));

    $response = $this->postJson('/api/participants', payloadParticipantValide());

    $response->assertStatus(422)->assertJsonValidationErrors('cin');
});

it('rejects creation when categorie_sp is not C, E, or O', function () {
    $response = $this->postJson('/api/participants', payloadParticipantValide(['categorie_sp' => 'X']));

    $response->assertStatus(422)->assertJsonValidationErrors('categorie_sp');
});

it('updates a participant with a partial payload', function () {
    $participant = Participant::create(payloadParticipantValide(['nom' => 'Ancien']));

    $this->putJson("/api/participants/{$participant->id}", ['nom' => 'Nouveau'])
        ->assertOk()
        ->assertJsonPath('data.nom', 'Nouveau');
});

it('deletes a participant', function () {
    $participant = Participant::create(payloadParticipantValide());

    $this->deleteJson("/api/participants/{$participant->id}")->assertOk();
    $this->assertDatabaseMissing('participants', ['id' => $participant->id]);
});
