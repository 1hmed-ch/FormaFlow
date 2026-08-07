<?php

use App\Enums\GerantGender;
use App\Exceptions\SuppressionBloqueeException;
use App\Filament\Resources\EntrepriseClientes\Pages\CreateEntrepriseCliente;
use App\Filament\Resources\EntrepriseClientes\Pages\EditEntrepriseCliente;
use App\Filament\Resources\EntrepriseClientes\Pages\ListEntrepriseClientes;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// 1. LISTE

it('affiche la page liste des entreprises clientes sans erreur', function () {
    Livewire::test(ListEntrepriseClientes::class)
        ->assertSuccessful();
});

it('affiche les entreprises existantes dans la table', function () {
    $entreprises = EntrepriseCliente::factory()->count(3)->create();

    Livewire::test(ListEntrepriseClientes::class)
        ->assertCanSeeTableRecords($entreprises);
});

// 2. CRÉATION

it('crée une entreprise cliente avec des données valides (y compris le gérant imbriqué)', function () {
    Livewire::test(CreateEntrepriseCliente::class)
        ->fillForm([
            'raison_sociale' => 'Atlas Industries',
            'ice' => '123456789012345', 
            'if' => '23456789',
            'email' => 'contact@atlas-industries.ma',
            'gerant' => [
                'nom' => 'El Amrani',
                'prenom' => 'Hicham',
                'cin' => 'AB123456',
                'genre' => GerantGender::Homme->value,
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('entreprise_clientes', [
        'raison_sociale' => 'Atlas Industries',
        'ice' => '123456789012345',
        'email' => 'contact@atlas-industries.ma',
    ]);

    $this->assertDatabaseHas('gerants', [
        'nom' => 'El Amrani',
        'cin' => 'AB123456',
    ]);
});

it('rejette la création si les champs obligatoires sont manquants', function () {
    Livewire::test(CreateEntrepriseCliente::class)
        ->fillForm([
            'raison_sociale' => '',
            'ice' => '',
            'if' => '',
            'email' => '',
            'gerant' => [
                'nom' => '',
                'prenom' => '',
                'cin' => '',
                'genre' => null,
            ],
        ])
        ->call('create')
        ->assertHasFormErrors([
            'raison_sociale' => 'required',
            'ice' => 'required',
            'if' => 'required',
            'email' => 'required',
            'gerant.nom' => 'required',
            'gerant.prenom' => 'required',
            'gerant.cin' => 'required',
            'gerant.genre' => 'required',
        ]);
});

it('rejette la création si l\'ICE ne fait pas exactement 15 caractères', function () {
    Livewire::test(CreateEntrepriseCliente::class)
        ->fillForm([
            'raison_sociale' => 'Test ICE invalide',
            'ice' => '12345', // trop court
            'if' => '23456789',
            'email' => 'test-ice@example.com',
            'gerant' => [
                'nom' => 'Test',
                'prenom' => 'Gérant',
                'cin' => 'CD999999',
                'genre' => GerantGender::Femme->value,
            ],
        ])
        ->call('create')
        ->assertHasFormErrors(['ice']);
});

it('rejette la création si l\'email est déjà utilisé', function () {
    EntrepriseCliente::factory()->create(['email' => 'existe@example.com']);

    Livewire::test(CreateEntrepriseCliente::class)
        ->fillForm([
            'raison_sociale' => 'Doublon Email',
            'ice' => '111111111111111',
            'if' => '11111111',
            'email' => 'existe@example.com',
            'gerant' => [
                'nom' => 'Test',
                'prenom' => 'Doublon',
                'cin' => 'EF888888',
                'genre' => GerantGender::Homme->value,
            ],
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});

// 3. MODIFICATION

it('modifie une entreprise cliente existante', function () {
    $entreprise = EntrepriseCliente::factory()->create(['raison_sociale' => 'Ancienne Raison']);

    Livewire::test(EditEntrepriseCliente::class, ['record' => $entreprise->id])
        ->fillForm([
            'raison_sociale' => 'Nouvelle Raison',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('entreprise_clientes', [
        'id' => $entreprise->id,
        'raison_sociale' => 'Nouvelle Raison',
    ]);
});

// 4. SUPPRESSION

it('supprime une entreprise cliente depuis la table quand elle n\'a ni formation ni participant', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    Livewire::test(ListEntrepriseClientes::class)
        ->callTableAction('delete', record: $entreprise);

    $this->assertDatabaseMissing('entreprise_clientes', ['id' => $entreprise->id]);
});

it('bloque la suppression depuis le panel quand l\'entreprise a des formations', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Formation::factory()->create(['entreprise_id' => $entreprise->id]);

    expect(fn () => Livewire::test(ListEntrepriseClientes::class)
        ->callTableAction('delete', record: $entreprise))
        ->toThrow(SuppressionBloqueeException::class);

    $this->assertDatabaseHas('entreprise_clientes', ['id' => $entreprise->id]);
});

it('bloque la suppression depuis le panel quand l\'entreprise a des participants', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    Participant::factory()->create(['entreprise_id' => $entreprise->id]);

    expect(fn () => Livewire::test(ListEntrepriseClientes::class)
        ->callTableAction('delete', record: $entreprise))
        ->toThrow(SuppressionBloqueeException::class);

    $this->assertDatabaseHas('entreprise_clientes', ['id' => $entreprise->id]);
});