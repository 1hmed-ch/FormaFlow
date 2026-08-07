<?php

namespace Database\Factories;

use App\Enums\CategorieSP;
use App\Models\EntrepriseCliente;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'entreprise_id' => EntrepriseCliente::factory(),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'cin' => strtoupper(fake()->unique()->bothify('??######')), 
            'numero_cnss' => fake()->unique()->numerify('#######'),      
            'fonction_occupee' => fake()->jobTitle(),
            'categorie_sp' => fake()->randomElement(CategorieSP::cases()),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->numerify('06########'),
        ];
    }
}