<?php

namespace Database\Seeders;

use App\Models\EntrepriseCliente;
use Illuminate\Database\Seeder;

class EntrepriseClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EntrepriseCliente::factory(20)->create();
    }
}
