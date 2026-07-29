<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table d'archive générique pour tout document PDF généré par la
     * plateforme (Modèle 5, Modèle 6, fiche d'évaluation, G1-G7 GIAC,
     * F3 OFPPT...).
     *
     * Rattachée de façon polymorphe (documentable) plutôt que par une
     * clé étrangère dédiée par entité, car les documents concernent
     * aujourd'hui EntrepriseCliente et pourraient demain concerner
     * Formation ou Groupe directement sans nouvelle migration.
     *
     * Chaque génération crée une nouvelle ligne (jamais de mise à jour
     * sur régénération) : le couple (documentable, type_document, version)
     * permet de conserver l'historique complet plutôt que d'écraser le
     * fichier précédent.
     */
    public function up(): void
    {
        Schema::create('documents_generes', function (Blueprint $table) {
            $table->id();

            $table->morphs('documentable');

            $table->string('type_document');
            $table->string('categorie');
            $table->string('nom_fichier');
            $table->string('disque');
            $table->string('chemin');
            $table->unsignedBigInteger('taille')->nullable();

            $table->unsignedInteger('version')->default(1);
            $table->string('statut')->default('genere');

            $table->foreignId('genere_par')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('genere_le');
            $table->json('metadonnees')->nullable();

            $table->timestamps();

            $table->index('type_document');
            $table->index('categorie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_generes');
    }
};
