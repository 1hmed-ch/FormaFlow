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
    Schema::create('formations', function (Blueprint $table) {
        $table->id();
        $table->string('intitule');
        $table->date('date_debut');
        $table->date('date_fin');
        $table->enum('statut', ['Planifiee', 'En cours', 'Terminee', 'Annulee'])->default('Planifiee');
        $table->foreignId('entreprise_id')->constrained('entreprise_clientes')->onDelete('cascade');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
