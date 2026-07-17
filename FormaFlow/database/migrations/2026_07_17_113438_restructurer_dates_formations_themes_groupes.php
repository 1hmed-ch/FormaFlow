<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Formation : Supprimer date_debut et date_fin
        Schema::table('formations', function (Blueprint $table) {
            if (Schema::hasColumn('formations', 'date_debut')) {
                $table->dropColumn('date_debut');
            }
            if (Schema::hasColumn('formations', 'date_fin')) {
                $table->dropColumn('date_fin');
            }
        });

        // 2. Thème : Supprimer duree, et Ajouter date_debut et date_fin
        Schema::table('themes', function (Blueprint $table) {
            if (Schema::hasColumn('themes', 'duree_prevue')) {
                $table->dropColumn('duree_prevue');
            }
            if (!Schema::hasColumn('themes', 'date_debut')) {
                $table->date('date_debut')->nullable()->after('intitule');
            }
            if (!Schema::hasColumn('themes', 'date_fin')) {
                $table->date('date_fin')->nullable()->after('date_debut');
            }
        });

        // 3. Groupe : Supprimer date_debut et date_fin
        Schema::table('groupes', function (Blueprint $table) {
            if (Schema::hasColumn('groupes', 'date_debut')) {
                $table->dropColumn('date_debut');
            }
            if (Schema::hasColumn('groupes', 'date_fin')) {
                $table->dropColumn('date_fin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('groupes', function (Blueprint $table) {
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->integer('duree_prevue')->nullable();
            $table->dropColumn(['date_debut', 'date_fin']);
        });

        Schema::table('formations', function (Blueprint $table) {
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
        });
    }
};