<?php

use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Participant;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function creerParticipant(int $entrepriseId, array $overrides = []): Participant
{
    return Participant::create(array_merge([
        'nom' => 'Test',
        'prenom' => fake()->firstName(),
        'cin' => strtoupper(fake()->unique()->bothify('??######')),
        'categorie_sp' => 'C',
        'entreprise_id' => $entrepriseId,
    ], $overrides));
}

// --- basic CRUD ----------------------------------------------------------

it('lists groupes with theme and participants loaded', function () {
    Groupe::factory()->count(2)->create();

    $this->getJson('/api/groupes')->assertOk()->assertJsonCount(2, 'data');
});

it('creates a groupe from a valid payload', function () {
    $theme = Theme::factory()->create();

    $response = $this->postJson('/api/groupes', [
        'libelle' => 'Groupe A',
        'lieu' => 'Casablanca',
        'effectif_max' => 15,
        'theme_id' => $theme->id,
    ]);

    $response->assertCreated()->assertJsonPath('data.libelle', 'Groupe A');
});

it('rejects creation when effectif_max is less than 1', function () {
    $theme = Theme::factory()->create();

    $response = $this->postJson('/api/groupes', [
        'libelle' => 'Groupe A',
        'effectif_max' => 0,
        'theme_id' => $theme->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('effectif_max');
});

it('deletes a groupe', function () {
    $groupe = Groupe::factory()->create();

    $this->deleteJson("/api/groupes/{$groupe->id}")->assertOk();
    $this->assertDatabaseMissing('groupes', ['id' => $groupe->id]);
});

// --- attachParticipants: the interesting business rules ----------------------

it('attaches participants that belong to the correct entreprise and have room', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $formation = Formation::factory()->create(['entreprise_id' => $entreprise->id]);
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);
    $groupe = Groupe::factory()->create(['theme_id' => $theme->id, 'effectif_max' => 10]);

    $p1 = creerParticipant($entreprise->id);
    $p2 = creerParticipant($entreprise->id);

    $response = $this->postJson("/api/groupes/{$groupe->id}/participants", [
        'participant_ids' => [$p1->id, $p2->id],
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('rejected', []);

    expect($groupe->fresh()->participants)->toHaveCount(2);
});

it('rejects a participant that belongs to a different entreprise than the formation', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $autreEntreprise = EntrepriseCliente::factory()->create();
    $formation = Formation::factory()->create(['entreprise_id' => $entreprise->id]);
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);
    $groupe = Groupe::factory()->create(['theme_id' => $theme->id, 'effectif_max' => 10]);

    $intrus = creerParticipant($autreEntreprise->id);

    $response = $this->postJson("/api/groupes/{$groupe->id}/participants", [
        'participant_ids' => [$intrus->id],
    ]);

    $response->assertOk()
        ->assertJsonPath('attached', [])
        ->assertJsonCount(1, 'rejected');

    expect($groupe->fresh()->participants)->toHaveCount(0);
});

it('rejects a participant already assigned to a different groupe in the same theme', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $formation = Formation::factory()->create(['entreprise_id' => $entreprise->id]);
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);
    $groupeA = Groupe::factory()->create(['theme_id' => $theme->id, 'effectif_max' => 10]);
    $groupeB = Groupe::factory()->create(['theme_id' => $theme->id, 'effectif_max' => 10]);

    $participant = creerParticipant($entreprise->id);
    $groupeA->participants()->attach($participant->id);

    $response = $this->postJson("/api/groupes/{$groupeB->id}/participants", [
        'participant_ids' => [$participant->id],
    ]);

    $response->assertOk()->assertJsonCount(1, 'rejected');
    expect($groupeB->fresh()->participants)->toHaveCount(0);
    // ...and still in their original groupe, untouched.
    expect($groupeA->fresh()->participants)->toHaveCount(1);
});

it('refuses the whole batch with 422 when it would exceed effectif_max', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $formation = Formation::factory()->create(['entreprise_id' => $entreprise->id]);
    $theme = Theme::factory()->create(['formation_id' => $formation->id]);
    $groupe = Groupe::factory()->create(['theme_id' => $theme->id, 'effectif_max' => 1]);

    $p1 = creerParticipant($entreprise->id);
    $p2 = creerParticipant($entreprise->id);

    $response = $this->postJson("/api/groupes/{$groupe->id}/participants", [
        'participant_ids' => [$p1->id, $p2->id],
    ]);

    $response->assertStatus(422)->assertJsonPath('success', false);
    expect($groupe->fresh()->participants)->toHaveCount(0);
});

it('detaches a participant from a groupe', function () {
    $groupe = Groupe::factory()->create();
    $participant = creerParticipant(EntrepriseCliente::factory()->create()->id);
    $groupe->participants()->attach($participant->id);

    $this->deleteJson("/api/groupes/{$groupe->id}/participants/{$participant->id}")
        ->assertOk();

    expect($groupe->fresh()->participants)->toHaveCount(0);
});
