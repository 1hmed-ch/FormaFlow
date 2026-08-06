<?php

namespace Database\Factories;

use App\Models\Groupe;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Groupe>
 */
class GroupeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'theme_id' => Theme::factory(),
            'libelle' => 'Groupe '.fake()->randomLetter(),
            'lieu' => fake()->city(),
            'effectif_max' => fake()->numberBetween(10, 30),
        ];
    }
}
