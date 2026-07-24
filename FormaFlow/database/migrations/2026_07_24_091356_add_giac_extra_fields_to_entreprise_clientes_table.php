<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            // Montant de la Taxe de Formation Professionnelle (TFP)
            $table->decimal('montant_tfp', 12, 2)->nullable()->after('num_cnss');

            // Historique GIAC (Pour la Fiche C)
            $table->boolean('deja_depose_giac')->default(false)->after('montant_tfp');
            $table->string('nom_ancien_giac')->nullable()->after('deja_depose_giac');
            $table->date('date_depot_ancien_giac')->nullable()->after('nom_ancien_giac');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->dropColumn([
                'montant_tfp',
                'deja_depose_giac',
                'nom_ancien_giac',
                'date_depot_ancien_giac',
            ]);
        });
    }
};