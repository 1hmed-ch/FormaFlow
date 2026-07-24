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
            $table->string('nom_groupe_etranger')->nullable();
            $table->text('references')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_formations', function (Blueprint $table) {
            $table->dropColumn(['nom_groupe_etranger', 'references']);
        });
    }
};
