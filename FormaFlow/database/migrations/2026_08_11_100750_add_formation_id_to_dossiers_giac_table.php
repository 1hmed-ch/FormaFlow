<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dossiers_giac', function (Blueprint $table) {
            $table->foreignId('formation_id')
                ->nullable()
                ->after('entreprise_cliente_id')
                ->constrained('formations')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dossiers_giac', function (Blueprint $table) {
            $table->dropConstrainedForeignId('formation_id');
        });
    }
};
