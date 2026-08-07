<?php

use App\Models\Groupe;
use App\Models\Participant;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a theme', function () {
    $theme = Theme::factory()->create();
    $groupe = Groupe::factory()->create(['theme_id' => $theme->id]);

    expect($groupe->theme->id)->toBe($theme->id);
});

it('can attach participants through the groupe_participant pivot', function () {
    $groupe = Groupe::factory()->create();
    $participant = Participant::create([
        'nom' => 'Bennani',
        'prenom' => 'Sara',
        'cin' => 'AB999999',
        'categorie_sp' => 'E',
        'entreprise_id' => \App\Models\EntrepriseCliente::factory()->create()->id,
    ]);

    $groupe->participants()->attach($participant->id);

    expect($groupe->participants)->toHaveCount(1)
        ->and($groupe->participants->first()->id)->toBe($participant->id);

    // And the inverse should hold too, via the pivot -- a good sanity check
    // that withTimestamps() and the pivot table itself are wired correctly.
    $this->assertDatabaseHas('groupe_participant', [
        'groupe_id' => $groupe->id,
        'participant_id' => $participant->id,
    ]);
});
