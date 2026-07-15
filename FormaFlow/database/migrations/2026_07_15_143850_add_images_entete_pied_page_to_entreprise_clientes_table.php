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

            // Chemins (sur le disque de stockage Filament) des images d'en-tête
            // et de pied de page propres à l'entreprise, utilisées pour habiller
            // le Modèle 6 et la Fiche de présence générés pour cette entreprise.
            $table->string('image_entete')->nullable()->after('contact_ref');
            $table->string('image_pied_page')->nullable()->after('image_entete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->dropColumn(['image_entete', 'image_pied_page']);
        });
    }
};
