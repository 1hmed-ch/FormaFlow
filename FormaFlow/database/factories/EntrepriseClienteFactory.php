<?php

namespace Database\Factories;

use App\Models\EntrepriseCliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EntrepriseCliente>
 */
class EntrepriseClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'raison_sociale' => fake()->company(),
            'siege_social' => fake()->address(),
            'date_creation' => fake()->date(),
            'statut_juridique' => fake()->randomElement(['SARL', 'SARL AU', 'SA', 'SNC']),
            'ice' => fake()->numerify('###############'), // 15 digits
            'if' => fake()->numerify('########'), // 8 digits
            'num_cnss' => fake()->numerify('#######'), // 7 digits
            'rc' => fake()->numerify('######'),
            'patente' => fake()->numerify('########'),
            'secteur_activite' => fake()->word(),
            'activite' => fake()->sentence(3),
            'effectif_total' => fake()->numberBetween(10, 500),
            'telephone' => fake()->phoneNumber(),
            'fax' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'contact_ref' => fake()->name(),
        ];
    }
}
