<?php

use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function payloadThemeValide(array $overrides = []): array
{
    return array_merge([
        'intitule' => 'Introduction à Laravel',
        'date_debut' => '2026-03-01',
        'date_fin' => '2026-03-05',
        'formation_id' => Formation::factory()->create()->id,
        'formateur_id' => Formateur::factory()->create()->id,
    ], $overrides);
}

it('lists themes with formation and formateur loaded', function () {
    Theme::factory()->count(2)->create();

    $response = $this->getJson('/api/themes');

    $response->assertOk()->assertJsonCount(2, 'data');
    expect($response->json('data.0.formation'))->not->toBeNull();
    expect($response->json('data.0.formateur'))->not->toBeNull();
});

it('shows a single theme', function () {
    $theme = Theme::factory()->create();

    $this->getJson("/api/themes/{$theme->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $theme->id);
});

it('creates a theme from a valid payload', function () {
    $response = $this->postJson('/api/themes', payloadThemeValide());

    $response->assertCreated()->assertJsonPath('data.intitule', 'Introduction à Laravel');
});

it('rejects creation when date_fin is before date_debut', function () {
    $response = $this->postJson('/api/themes', payloadThemeValide([
        'date_debut' => '2026-03-10',
        'date_fin' => '2026-03-01',
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors('date_fin');
});

it('rejects creation when formation_id does not exist', function () {
    $response = $this->postJson('/api/themes', payloadThemeValide(['formation_id' => 999999]));

    $response->assertStatus(422)->assertJsonValidationErrors('formation_id');
});

it('rejects creation when formateur_id does not exist', function () {
    $response = $this->postJson('/api/themes', payloadThemeValide(['formateur_id' => 999999]));

    $response->assertStatus(422)->assertJsonValidationErrors('formateur_id');
});

it('updates a theme with a partial payload', function () {
    $theme = Theme::factory()->create(['intitule' => 'Ancien']);

    $this->putJson("/api/themes/{$theme->id}", ['intitule' => 'Nouveau'])
        ->assertOk()
        ->assertJsonPath('data.intitule', 'Nouveau');
});

it('deletes a theme with no groupes attached', function () {
    $theme = Theme::factory()->create();

    $this->deleteJson("/api/themes/{$theme->id}")->assertOk();
    $this->assertDatabaseMissing('themes', ['id' => $theme->id]);
});
