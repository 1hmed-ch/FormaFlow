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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('cin')->unique();
            $table->string('email')->unique();
            $table->string('nummero_cnss')->unique();
            $table->string('telephone')->nullable();
            $table->string('fonction_occupe')->nullable();
            $table->enum('categorie_sp', ['C', 'E', 'O'])->comment('Categorie SP(socio-professionnelle) : C = Cadre, E = Employé, O = Ouvrier');

            $table->foreignId('entreprise_id')->constrained('entreprise_clientes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
