<?php

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);
it('affiche la page de connexion sans erreur pour un visiteur', function () {
    $this->get(route('filament.admin.auth.login'))->assertSuccessful();
});

it('connecte un utilisateur avec des identifiants valides', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password123',
        ])
        ->call('authenticate');

    $this->assertAuthenticatedAs($user);
});

it('rejette une connexion avec un mot de passe invalide', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'mauvais-mot-de-passe',
        ])
        ->call('authenticate');

    $this->assertGuest();
});