<?php

namespace Database\Factories;

use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Theme>
 */
class ThemeFactory extends Factory
{
    public function definition(): array
    {
        $debut = fake()->dateTimeBetween('-3 months', '+1 month');

        return [
            'formation_id' => Formation::factory(),
            'formateur_id' => Formateur::factory(),
            'intitule' => fake()->sentence(3),
            'date_debut' => $debut,
            'date_fin' => (clone $debut)->modify('+'.fake()->numberBetween(1, 10).' days'),
            'objectifs' => fake()->paragraph(),
        ];
    }
}
