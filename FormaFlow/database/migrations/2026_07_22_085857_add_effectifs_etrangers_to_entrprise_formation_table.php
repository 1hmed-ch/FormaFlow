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
        Schema::table('entreprise_formations', function (Blueprint $table) {
            $table->unsignedSmallInteger('nb_experts_permanents_etrangers')->default(0)->after('nb_experts_permanents');
            $table->unsignedSmallInteger('nb_experts_vacataires_etrangers')->default(0)->after('nb_experts_vacataires');
            $table->unsignedSmallInteger('nb_animateurs_formateurs_etrangers')->default(0)->after('nb_animateurs_formateurs');
            $table->unsignedSmallInteger('nb_autres_employes_etrangers')->default(0)->after('nb_autres_employes');
            $table->boolean('appartient_groupe_etranger')->default(false)->after('effectif_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_formations', function (Blueprint $table) {
            Schema::table('entreprise_formations', function (Blueprint $table) {
                $table->dropColumn([
                    'nb_experts_permanents_etrangers',
                    'nb_experts_vacataires_etrangers',
                    'nb_animateurs_formateurs_etrangers',
                    'nb_autres_employes_etrangers',
                    'appartient_groupe_etranger',
                ]);
            });
        });
    }
};
