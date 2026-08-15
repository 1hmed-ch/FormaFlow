<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etudes_diagnostic_strategique', function (Blueprint $table) {
            $table->foreignId('formation_id')
                ->nullable()
                ->after('entreprise_id')
                ->constrained('formations')
                ->nullOnDelete();

            $table->unique('formation_id');
        });
    }

    public function down(): void
    {
        Schema::table('etudes_diagnostic_strategique', function (Blueprint $table) {
            $table->dropUnique(['formation_id']);
            $table->dropConstrainedForeignId('formation_id');
        });
    }
};
