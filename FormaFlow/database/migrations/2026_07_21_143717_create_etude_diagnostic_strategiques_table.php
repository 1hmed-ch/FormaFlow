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
        Schema::create('etudes_diagnostic_strategique', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entreprise_id')
                ->constrained('entreprise_clientes')
                ->cascadeOnDelete();

            // NATURE du PROJET de DEVELOPPEMENT de l'ENTREPRISE (cases à cocher)
            $table->boolean('projet_marche_export')->default(false);
            $table->boolean('projet_investissement_techno')->default(false);
            $table->boolean('projet_mise_aux_normes')->default(false);
            $table->boolean('projet_autre')->default(false);
            $table->string('projet_autre_precision')->nullable();

            // OBJECTIFS et RESULTATS ATTENDUS du DIAGNOSTIC
            $table->text('objectifs_resultats_attendus')->nullable();

            // PROPOSITION D'INTERVENTION DU CABINET-CONSEIL
            $table->string('prestations_envisagees')->nullable();
            $table->unsignedInteger('annee_application')->nullable();
            $table->unsignedInteger('duree_intervention_jours')->nullable();
            $table->date('date_demarrage')->nullable();
            $table->decimal('cout_previsionnel', 10, 2)->nullable();
            $table->date('date_signature')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etudes_diagnostic_strategique');
    }
};
