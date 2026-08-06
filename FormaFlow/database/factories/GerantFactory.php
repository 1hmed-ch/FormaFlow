<?php

namespace Database\Factories;

use App\Enums\GerantGender;
use App\Models\Gerant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gerant>
 */
class GerantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'genre' => fake()->randomElement(GerantGender::cases()),
            'fonction' => fake()->jobTitle(),
            'cin' => strtoupper(fake()->unique()->bothify('??######')),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
        ];
    }
}
