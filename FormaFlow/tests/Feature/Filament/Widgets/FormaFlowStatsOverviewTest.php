<?php

use App\Enums\FormationStatus;
use App\Filament\Widgets\FormaFlowStatsOverview;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('affiche les bons compteurs sur le widget de statistiques', function () {
    EntrepriseCliente::factory()->count(4)->create();
    Formation::factory()->count(3)->create(); // Planifiées
    Formation::factory()->count(2)->create(['statut' => FormationStatus::TERMINEE]);
    Participant::factory()->count(7)->create();

    Livewire::test(FormaFlowStatsOverview::class)
        ->assertSuccessful()
        ->assertSee('4') // Entreprises Clientes
        ->assertSee('5') // Nombre de Formations total (3 + 2)
        ->assertSee('2 formations terminées')
        ->assertSee('7'); // Participants
});