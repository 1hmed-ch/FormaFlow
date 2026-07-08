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
        Schema::create('themes', function (Blueprint $table) {
    $table->id();
    $table->string('intitule');
    $table->integer('duree_prevue');
    $table->text('objectifs')->nullable();
    $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
    $table->foreignId('formateur_id')->constrained('formateurs')->onDelete('cascade');
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
