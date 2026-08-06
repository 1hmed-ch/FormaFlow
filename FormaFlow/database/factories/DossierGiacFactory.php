<?php

namespace Database\Factories;

use App\Enums\StatutDossierGiac;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DossierGiac>
 */
class DossierGiacFactory extends Factory
{
    public function definition(): array
    {
        return [
            'entreprise_cliente_id' => EntrepriseCliente::factory(),
            'statut' => StatutDossierGiac::EnCours,
            'date_generation' => null,
            'chemin_stockage' => null,
        ];
    }
}
