<?php

use App\Enums\GerantGender;
use App\Models\EntrepriseCliente;
use App\Models\Gerant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('casts genre to the GerantGender enum', function () {
    $gerant = Gerant::factory()->create(['genre' => GerantGender::Femme]);

    expect($gerant->genre)->toBe(GerantGender::Femme);
});

it('has one entreprise cliente', function () {
    $gerant = Gerant::factory()->create();
    EntrepriseCliente::factory()->create(['gerant_id' => $gerant->id]);

    expect($gerant->entrepriseCliente)->toBeInstanceOf(EntrepriseCliente::class);
});
