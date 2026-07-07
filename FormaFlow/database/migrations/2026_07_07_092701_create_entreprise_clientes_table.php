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
        Schema::create('entreprise_clientes', function (Blueprint $table) {
            $table->id(); 
            
            $table->string('raisonSociale');
            $table->string('siegeSocial');
            $table->date('dateCreation')->nullable();
            $table->string('statutJuridique')->nullable();
            
            $table->string('ice', 15)->unique(); 
            $table->string('if', 50)->unique();  
            
            $table->string('numCnss', 50)->nullable()->unique();
            $table->string('rc', 50)->nullable()->unique();
            $table->string('patente', 50)->nullable()->unique();
            
            $table->string('secteurActivite');
            $table->string('activite')->nullable();
            $table->string('regionAffiliationCnss')->nullable();
            $table->string('effectifTotal')->nullable();

            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->unique(); 
            
            $table->string('contactRef')->nullable();

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprise_clientes');
    }
};