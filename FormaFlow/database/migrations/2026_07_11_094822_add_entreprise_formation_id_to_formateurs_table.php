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
        Schema::table('formateurs', function (Blueprint $table) {
            $table->foreignId('entreprise_formation_id')
                  ->after('id')
                  ->constrained('entreprise_formations')
                  ->restrictOnDelete(); // Bloque la suppression si un formateur y est lié
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
    {
        Schema::table('formateurs', function (Blueprint $table) {
            $table->dropForeign(['entreprise_formation_id']);
            $table->dropColumn('entreprise_formation_id');
        });
    }
};
