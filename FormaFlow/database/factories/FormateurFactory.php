<?php

namespace Database\Factories;

use App\Enums\FormateurStatus;
use App\Models\EntrepriseFormation;
use App\Models\Formateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Formateur>
 */
class FormateurFactory extends Factory
{
    public function definition(): array
    {
        return [
            // EntrepriseFormation::factory() creates a brand-new organisme
            // for every Formateur by default. In tests where several
            // formateurs must share the same organisme, override this:
            // Formateur::factory()->for($organisme, 'organisme')->create();
            'entreprise_formation_id' => EntrepriseFormation::factory(),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'specialite' => fake()->jobTitle(),
            'statut' => fake()->randomElement(FormateurStatus::cases()),
        ];
    }
}
