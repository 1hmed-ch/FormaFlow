<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->integer('effectif_cadre')->default(0)->after('effectif_total');
            $table->integer('effectif_cadre_moyen')->default(0)->after('effectif_cadre');
            $table->integer('effectif_agent_qualifie')->default(0)->after('effectif_cadre_moyen');
            $table->integer('effectif_agent_sans_qualification')->default(0)->after('effectif_agent_qualifie');
            $table->integer('effectif_agent_occasionnel')->default(0)->after('effectif_agent_sans_qualification');
        });
    }

    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->dropColumn([
                'effectif_cadre',
                'effectif_cadre_moyen',
                'effectif_agent_qualifie',
                'effectif_agent_sans_qualification',
                'effectif_agent_occasionnel',
            ]);
        });
    }
};