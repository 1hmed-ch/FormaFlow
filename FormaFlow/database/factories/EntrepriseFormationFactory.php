<?php

namespace Database\Factories;

use App\Models\EntrepriseFormation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EntrepriseFormation>
 */
class EntrepriseFormationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'raison_sociale' => fake()->company(),
            'siege_social' => fake()->address(),
            'ville' => fake()->city(),
            'gerant_nom' => fake()->lastName(),
            'gerant_prenom' => fake()->firstName(),
            'date_creation' => fake()->date(),
            'statut_juridique' => fake()->randomElement(['SARL', 'SARL AU', 'SA']),
            'activite' => 'Formation',
            'ice' => fake()->numerify('###############'),
            'rc' => fake()->numerify('#####'),
            'if' => fake()->numerify('########'),
            'patente' => fake()->numerify('########'),
            'telephone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'domaines_competence' => [],
            'moyens_pedagogiques' => [],
            'nb_experts_permanents' => fake()->numberBetween(0, 10),
            'nb_experts_vacataires' => fake()->numberBetween(0, 10),
            'nb_animateurs_formateurs' => fake()->numberBetween(0, 10),
            'nb_autres_employes' => fake()->numberBetween(0, 5),
            'effectif_total' => fake()->numberBetween(1, 50),
            'appartient_groupe_etranger' => false,
            'representant_nom' => fake()->name(),
            'representant_fonction' => 'Gérant',
        ];
    }
}
