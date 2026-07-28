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
    Schema::create('entreprise_formations', function (Blueprint $table) {
        $table->id();

        // 1. Informations Générales & Administratives
        $table->string('raison_sociale');
        $table->string('logo')->nullable();
        $table->string('siege_social');
        $table->string('gerant_nom');
        $table->string('gerant_prenom');
        $table->date('date_creation');
        $table->string('statut_juridique');
        $table->string('activite');

        // 2. Infos Fiscales & Coordonnées
        $table->string('ice');
        $table->string('rc');
        $table->string('if');
        $table->string('patente');
        $table->string('cnss')->nullable();
        $table->string('capital_social')->nullable();
        $table->string('telephone');
        $table->string('fax')->nullable();
        $table->string('email');
        $table->string('site_web')->nullable();

        // 3. Solution JSON flexible pour les Domaines & Moyens
        $table->json('domaines_competence')->nullable();
        $table->json('moyens_pedagogiques')->nullable();

        // 4. Effectifs globaux
        $table->integer('nb_experts_permanents')->default(0);
        $table->integer('nb_experts_vacataires')->default(0);
        $table->integer('nb_animateurs_formateurs')->default(0);
        $table->integer('nb_autres_employes')->default(0);
        $table->integer('effectif_total')->default(0);

        // 5. Représentant Légal & Signature pour les PDFs
        $table->string('representant_nom');
        $table->string('representant_fonction');
        $table->string('signature')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprise_formations');
    }
};
