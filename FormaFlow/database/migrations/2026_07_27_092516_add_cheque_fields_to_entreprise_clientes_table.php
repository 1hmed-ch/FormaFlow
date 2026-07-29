<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->string('cheque_banque')->nullable();
            $table->string('cheque_numero')->nullable();
            $table->date('cheque_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->dropColumn(['cheque_banque', 'cheque_numero', 'cheque_date']);
        });
    }
};
