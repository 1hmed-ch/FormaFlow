<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->enum('statut_demande_financement', ['en_cours', 'acceptee', 'refusee', 'archivee'])->nullable()->after('ofppt_mdp');
        });
    }

    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->dropColumn('statut_demande_financement');
        });
    }
};
