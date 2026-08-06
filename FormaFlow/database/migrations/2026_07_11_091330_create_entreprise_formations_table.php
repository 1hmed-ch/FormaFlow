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

            // 1. Informations Générales & Administratives (Ghir raison_sociale li obligatoire)
            $table->string('raison_sociale');
            $table->string('logo')->nullable();
            $table->string('siege_social')->nullable();
            $table->string('gerant_nom')->nullable();
            $table->string('gerant_prenom')->nullable();
            $table->date('date_creation')->nullable();
            $table->string('statut_juridique')->nullable();
            $table->string('activite')->nullable();

            // 2. Infos Fiscales & Coordonnées (Kolchi nullable w ḥydna unique bach ma y-drouch conflit)
            $table->string('ice')->nullable();
            $table->string('rc')->nullable();
            $table->string('if')->nullable();
            $table->string('patente')->nullable();
            $table->string('cnss')->nullable();
            $table->string('capital_social')->nullable();
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();

            // 3. Solution JSON flexible
            $table->json('domaines_competence')->nullable();
            $table->json('moyens_pedagogiques')->nullable();

            // 4. Effectifs globaux (nullable wla default 0)
            $table->integer('nb_experts_permanents')->nullable()->default(0);
            $table->integer('nb_experts_vacataires')->nullable()->default(0);
            $table->integer('nb_animateurs_formateurs')->nullable()->default(0);
            $table->integer('nb_autres_employes')->nullable()->default(0);
            $table->integer('effectif_total')->nullable()->default(0);

            // 5. Représentant Légal & Signature
            $table->string('representant_nom')->nullable();
            $table->string('representant_fonction')->nullable();
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