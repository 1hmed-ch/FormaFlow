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
          
            $table->foreignId('gerant_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('gerants')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
        
            $table->dropForeign(['gerant_id']);
            $table->dropColumn('gerant_id');
        });
    }
};