<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EntrepriseFormation extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    public const PIECES_JOINTES = [
        'cv_consultants'        => 'CV des consultants',
        'proposition_intervention' => 'Proposition d\'intervention',
        'rc_modele_j'            => 'RC Modèle J',
        'eligibilite_csf'        => 'Éligibilité CSF cabinet',
        'facture_pro_forma'      => 'Facture pro forma (originale)',
    ];

    protected $fillable = [
        'raison_sociale',
        'logo',
        'siege_social',
        'ville',
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
        'nb_experts_permanents_etrangers',
        'nb_experts_vacataires',
        'nb_experts_vacataires_etrangers',
        'nb_animateurs_formateurs',
        'nb_animateurs_formateurs_etrangers',
        'nb_autres_employes',
        'nb_autres_employes_etrangers',
        'effectif_total',
        'appartient_groupe_etranger',
        'nom_groupe_etranger', 
        'references',
        'representant_nom',
        'representant_fonction',

    ];

    /**
     * Casting des attributs JSON en tableaux PHP
     */
    protected $casts = [
        'domaines_competence' => 'array',
        'moyens_pedagogiques' => 'array',
        'date_creation' => 'date',
        'appartient_groupe_etranger' => 'boolean',
    ];


    public function registerMediaCollections(): void
    {
        foreach (array_keys(self::PIECES_JOINTES) as $collection) {
            $this->addMediaCollection($collection)
                ->singleFile() // un seul fichier actif par type = auto-remplacement à l'upload
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
        }
    }



    public function getPieceJointeStatut(string $collection): array
    {
        $media = $this->getFirstMedia($collection);

        if (!$media) {
            return [
                'etat' => 'Manquant',
                'media' => null,
                'nom_fichier' => null,
                'date_ajout' => null,
                'date_maj' => null,
                'date_expiration' => null,
            ];
        }

        $expiration = $media->getCustomProperty('date_expiration');
        $etat = 'Valide';

        if ($expiration && \Carbon\Carbon::parse($expiration)->isPast()) {
            $etat = 'Expiré';
        }

        return [
            'etat' => $etat,
            'media' => $media,
            'nom_fichier' => $media->file_name,
            'date_ajout' => $media->created_at,
            'date_maj' => $media->updated_at,
            'date_expiration' => $expiration,
        ];
    }

    /**
     * Effectif total des étrangers, toutes fonctions confondues (calculé,
     * non stocké) — utilisé par G3 (GIAC) et le Formulaire F3 (OFPPT) dans
     * la ligne "Total" du tableau Moyens humains.
     */
    public function getEffectifTotalEtrangersAttribute(): int
    {
        return $this->nb_experts_permanents_etrangers
            + $this->nb_experts_vacataires_etrangers
            + $this->nb_animateurs_formateurs_etrangers
            + $this->nb_autres_employes_etrangers;
    }

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
            'siege_social'            => '6ème étage Imm El youbia, Ave Allal Ben Abdellah, Fes 30000',
            'ville'                   => 'Fes',
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
            'nb_experts_permanents_etrangers' => 0,
            'nb_experts_vacataires'   => 0,
            'nb_experts_vacataires_etrangers' => 0,
            'nb_animateurs_formateurs'=> 0,
            'nb_animateurs_formateurs_etrangers' => 0,
            'nb_autres_employes'      => 0,
            'nb_autres_employes_etrangers' => 0,
            'effectif_total'          => 0,
            'appartient_groupe_etranger' => false,
            'nom_groupe_etranger'                => null, 
            'references'                         => null, 
            // 5. Représentant Légal 
            'representant_nom'        => 'Nom Représentant',
            'representant_fonction'   => 'Gérant',
          
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


}
