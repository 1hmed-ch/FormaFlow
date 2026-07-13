<?php

namespace Database\Seeders;

use App\Models\EntrepriseFormation;
use Illuminate\Database\Seeder;

class EntrepriseFormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EntrepriseFormation::current();
    }
}
