<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['entreprise_id']);
            $table->foreign('entreprise_id')
                ->references('id')->on('entreprise_clientes')
                ->onDelete('restrict');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['entreprise_id']);
            $table->foreign('entreprise_id')
                ->references('id')->on('entreprise_clientes')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['entreprise_id']);
            $table->foreign('entreprise_id')->references('id')->on('entreprise_clientes')->onDelete('cascade');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['entreprise_id']);
            $table->foreign('entreprise_id')->references('id')->on('entreprise_clientes')->onDelete('cascade');
        });
    }
};