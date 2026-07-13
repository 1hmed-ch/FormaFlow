<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntrepriseFormation extends Model
{
  use HasFactory;
    protected $fillable = [
        'raison_sociale',
        'logo',
        'siege_social',
        'gerant_nom',
        'gerant_prenom',
        'date_creation',
        'statut_juridique',
        'activite',
        'ice',
        'rc',
        'if',
        'patente',
        'cnss',
        'capital_social',
        'telephone',
        'fax',
        'email',
        'site_web',
        'domaines_competence',
        'moyens_pedagogiques',
        'nb_experts_permanents',
        'nb_experts_vacataires',
        'nb_animateurs_formateurs',
        'nb_autres_employes',
        'effectif_total',
        'representant_nom',
        'representant_fonction',
        'signature'
    ];

    /**
     * Casting des attributs JSON en tableaux PHP
     */
    protected $casts = [
        'domaines_competence' => 'array',
        'moyens_pedagogiques' => 'array',
        'date_creation' => 'date',
    ];

    /**
     * Accesseur Singleton pour récupérer la configuration unique de Plénitude
     */
    public static function current(): self
{
    return self::firstOrCreate(
        ['id' => 1], // Force l'ID 1 pour garantir la fiche unique
        [
            // 1. Informations Générales & Administratives
            'raison_sociale'          => 'Plénitude Outsourcing (Test)',
            'logo'                    => null, // Nullable dans la migration
            'siege_social'            => 'Fès',
            'gerant_nom'              => 'Nom Gérant',
            'gerant_prenom'           => 'Prénom Gérant',
            'date_creation'           => now(),
            'statut_juridique'        => 'SARL',
            'activite'                => 'Formation',

            // 2. Infos Fiscales & Coordonnées
            'ice'                     => '000000000000000',
            'rc'                      => '00000',
            'if'                      => '00000000',
            'patente'                 => '00000000',
            'cnss'                    => null, // Nullable dans la migration
            'capital_social'          => null, // Nullable dans la migration
            'telephone'               => '0500000000',
            'fax'                     => null, // Nullable dans la migration
            'email'                   => 'admin@plenitude.ma',
            'site_web'                => null, // Nullable dans la migration

            // 3. Domaines & Moyens (JSON)
            'domaines_competence'     => [],
            'moyens_pedagogiques'     => [],

            // 4. Effectifs globaux (Integers)
            'nb_experts_permanents'   => 0,
            'nb_experts_vacataires'   => 0,
            'nb_animateurs_formateurs'=> 0,
            'nb_autres_employes'      => 0,
            'effectif_total'          => 0,

            // 5. Représentant Légal & Signature
            'representant_nom'        => 'Nom Représentant',
            'representant_fonction'   => 'Gérant',
            'signature'               => null, // Nullable dans la migration
        ]
    );
}

    /**
     * Une entreprise de formation possède plusieurs formateurs rattachés
     */
    public function formateurs(): HasMany
    {
        return $this->hasMany(Formateur::class, 'entreprise_formation_id');
    }

    /**
     * Une entreprise de formation dispense plusieurs sessions de formations
     */
    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class, 'entreprise_formation_id');
    }
}
