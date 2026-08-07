<?php

use App\Enums\CategorieSP;
use App\Filament\Resources\Participants\Pages\CreateParticipant;
use App\Filament\Resources\Participants\Pages\EditParticipant;
use App\Filament\Resources\Participants\Pages\ListParticipants;
use App\Models\EntrepriseCliente;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// 1. LISTE

it('affiche la page liste des participants sans erreur', function () {
    Livewire::test(ListParticipants::class)
        ->assertSuccessful();
});

it('affiche les participants existants dans la table', function () {
    $participants = Participant::factory()->count(3)->create();

    Livewire::test(ListParticipants::class)
        ->assertCanSeeTableRecords($participants);
});

//2. CRÉATION

it('crée un participant avec des données valides', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    Livewire::test(CreateParticipant::class)
        ->fillForm([
            'entreprise_id' => $entreprise->id,
            'nom' => 'Idrissi',
            'prenom' => 'Karim',
            'cin' => 'AB123456',
            'numero_cnss' => '9876543',
            'fonction_occupee' => 'Technicien',
            'categorie_sp' => CategorieSP::Ouvrier->value,
            'email' => 'karim.idrissi@example.com',
            'telephone' => '0612345678',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('participants', [
        'cin' => 'AB123456',
        'entreprise_id' => $entreprise->id,
    ]);
});

it('rejette la création si les champs obligatoires sont manquants', function () {
    Livewire::test(CreateParticipant::class)
        ->fillForm([
            'entreprise_id' => null,
            'nom' => '',
            'prenom' => '',
            'cin' => '',
            'categorie_sp' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'entreprise_id' => 'required',
            'nom' => 'required',
            'prenom' => 'required',
            'cin' => 'required',
            'categorie_sp' => 'required',
        ]);
});

it('rejette la création si le CIN est déjà utilisé', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Participant::factory()->create(['cin' => 'CD999999']);

    Livewire::test(CreateParticipant::class)
        ->fillForm([
            'entreprise_id' => $entreprise->id,
            'nom' => 'Test',
            'prenom' => 'Doublon',
            'cin' => 'CD999999',
            'categorie_sp' => CategorieSP::Cadre->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['cin']);
});

it('rejette la création si le numéro CNSS est déjà utilisé', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Participant::factory()->create(['numero_cnss' => '1112223']);

    Livewire::test(CreateParticipant::class)
        ->fillForm([
            'entreprise_id' => $entreprise->id,
            'nom' => 'Test',
            'prenom' => 'Doublon',
            'cin' => 'EF888888',
            'numero_cnss' => '1112223',
            'categorie_sp' => CategorieSP::Employe->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['numero_cnss']);
});

//3. MODIFICATION

it('modifie un participant existant', function () {
    $participant = Participant::factory()->create(['nom' => 'Ancien Nom']);

    Livewire::test(EditParticipant::class, ['record' => $participant->id])
        ->fillForm([
            'nom' => 'Nouveau Nom',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('participants', [
        'id' => $participant->id,
        'nom' => 'Nouveau Nom',
    ]);
});

it('permet de garder son propre CIN en modifiant un participant', function () {
    $participant = Participant::factory()->create(['cin' => 'GH777777']);

    Livewire::test(EditParticipant::class, ['record' => $participant->id])
        ->fillForm([
            'cin' => 'GH777777', // inchangé
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

//4. SUPPRESSION

it('supprime un participant depuis la table de liste', function () {
    $participant = Participant::factory()->create();

    Livewire::test(ListParticipants::class)
        ->callTableAction('delete', record: $participant);

    $this->assertDatabaseMissing('participants', ['id' => $participant->id]);
});