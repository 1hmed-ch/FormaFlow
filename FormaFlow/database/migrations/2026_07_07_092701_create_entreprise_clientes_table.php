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
            
            $table->string('raison_sociale');
            $table->string('siege_social');
            $table->date('date_creation')->nullable();
            $table->string('statut_juridique')->nullable();
            
            $table->string('ice', 15)->unique(); 
            $table->string('if', 50)->unique();  
            
            $table->string('num_cnss', 50)->nullable()->unique();
            $table->string('rc', 50)->nullable()->unique();
            $table->string('patente', 50)->nullable()->unique();
            
            $table->string('secteur_activite');
            $table->string('activite')->nullable();
            $table->string('region_affiliation_cnss')->nullable();
            $table->integer('effectif_total')->nullable();
            

            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->unique(); 
            
            $table->string('contact_ref')->nullable();

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