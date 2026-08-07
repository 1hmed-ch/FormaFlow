<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etudes_ingenierie_formation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entreprise_id')
                ->constrained('entreprise_clientes')
                ->cascadeOnDelete();

            $table->string('nature_action')->default("Ingénierie de Formation")->nullable();

            $table->text('diagnostic_besoins')->nullable();

            $table->string('plan_formation')->nullable();
            $table->unsignedInteger('plan_formation_annee')->nullable();

            $table->text('bilan_competence')->nullable();
            $table->text('autres_precisions')->nullable();

            $table->text('resultats_attendus')->nullable();

            $table->date('periode_debut')->nullable();
            $table->date('periode_fin')->nullable();

            $table->unsignedInteger('nb_jours_intervention')->nullable();

            $table->decimal('cout_action', 10, 2)->nullable();
            $table->date('date_signature')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etudes_ingenierie_formation');
    }
};
