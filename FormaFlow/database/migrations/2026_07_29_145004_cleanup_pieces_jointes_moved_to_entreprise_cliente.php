<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\EntrepriseFormation;

return new class extends Migration
{
    public function up(): void
    {
        $entreprise = EntrepriseFormation::current();

        foreach (['facture_pro_forma', 'eligibilite_csf'] as $collection) {
            $media = $entreprise->getFirstMedia($collection);
            if ($media) {
                $media->delete(); // supprime le fichier physique + la ligne dans `media`
            }
        }
    }

    public function down(): void
    {
        // Pas de rollback possible : les fichiers supprimés sont perdus.
        // Si besoin, restaurer depuis un backup manuel.
    }
};