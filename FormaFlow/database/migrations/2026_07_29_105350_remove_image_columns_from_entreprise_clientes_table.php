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
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->dropColumn(['image_entete', 'image_pied_page']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->string('image_entete')->nullable();
            $table->string('image_pied_page')->nullable();
        });
    }
};
