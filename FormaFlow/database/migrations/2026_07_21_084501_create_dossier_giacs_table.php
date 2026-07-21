<?php

use App\Enums\StatutDossierGiac;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers_giac', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('entreprise_cliente_id')
                  ->constrained('entreprise_clientes')
                  ->cascadeOnDelete();
            
            $table->enum('statut', array_column(StatutDossierGiac::cases(), 'value'))
                  ->default(StatutDossierGiac::EnCours->value);
            
            $table->timestamp('date_generation')->nullable();
            $table->string('chemin_stockage')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers_giac');
    }
};