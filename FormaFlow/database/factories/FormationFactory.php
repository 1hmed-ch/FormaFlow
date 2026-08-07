<?php

namespace Database\Factories;

use App\Enums\FormationStatus;
use App\Enums\TypeFormation;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Formation>
 */
class FormationFactory extends Factory
{
    public function definition(): array
    {
        $debut = fake()->dateTimeBetween('-6 months', '+1 month');

        return [
            'entreprise_id' => EntrepriseCliente::factory(),
            'intitule' => fake()->sentence(4),
            'date_debut' => $debut,
            'date_fin' => (clone $debut)->modify('+'.fake()->numberBetween(5, 60).' days'),
            'type_formation' => fake()->randomElement(TypeFormation::cases()),
            'statut' => FormationStatus::PLANIFIEE,
        ];
    }

    /**
     * State helper: a formation that has already finished.
     * Usage: Formation::factory()->terminee()->create();
     */
    public function terminee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => FormationStatus::TERMINEE,
        ]);
    }
}
