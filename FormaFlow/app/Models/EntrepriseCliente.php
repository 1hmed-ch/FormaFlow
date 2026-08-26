<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\DemandeFinancementStatus;
use App\Enums\FormationStatus;


class EntrepriseCliente extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'gerant_id',
        'raison_sociale',
        'siege_social',
        'ville',
        'date_creation',
        'statut_juridique',
        'ice',
        'num_cnss',
        'montant_tfp',
        'deja_depose_giac',
        'nom_ancien_giac',
        'date_depot_ancien_giac',
        'rc',
        'if',
        'patente',
        'secteur_activite',
        'activite',
        'region_affiliation_cnss',
        'effectif_total',
        'effectif_cadre',
        'effectif_cadre_moyen',
        'effectif_agent_qualifie',
        'effectif_agent_sans_qualification',
        'effectif_agent_occasionnel',
        'telephone',
        'fax',
        'email',
        'contact_ref',

        'cheque_banque',
        'cheque_numero',
        'cheque_date',
        'gmail_login_ofppt',
        'gmail_ofppt_mdp',
        'ofppt_mdp',
        'statut_demande_financement',
    ];

    protected $casts = [
        'date_creation' => 'date:Y-m-d',
        'deja_depose_giac' => 'boolean',
        'date_depot_ancien_giac' => 'date:Y-m-d',
        'montant_tfp' => 'decimal:2',
        'cheque_date'=>'date:Y-m-d',
        'statut_demande_financement' => \App\Enums\DemandeFinancementStatus::class,
    ];

    public const PIECES_JOINTES = [
        'cin_gerant'       => 'CIN du gérant',
        'entete_page'      => 'Entête de page',
        'pied_page'        => 'Pied de page',
        'logo'             => 'Logo',
        //'eligibilite_csf'    => 'Éligibilité CSF cabinet',
        //'facture_pro_forma'  => 'Facture pro forma (originale)',
       // 'autres_documents' => 'Autres documents',
    ];
    public const PIECES_JOINTES_OFPPT = [
    'formulaire_f2' => 'Formulaire F2',
    'formulaire_f3' => 'Formulaire F3',
    'modele_1' => 'Modèle 1',
    'rapport_valide_giac' => 'Rapport validé par le GIAC',
    'attestation_approbation_rapport' => "Attestation d'approbation du rapport",
    ];

    public function registerMediaCollections(): void
    {
        $singleCollections = ['cin_gerant', 'entete_page', 'pied_page', 'logo','eligibilite_csf', 'facture_pro_forma',
        'formulaire_f2', 'formulaire_f3', 'modele_1', 'rapport_valide_giac', 'attestation_approbation_rapport',];

        foreach ($singleCollections as $collection) {
            $this->addMediaCollection($collection)
                ->singleFile()
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->useDisk('local');
        }

        $this->addMediaCollection('autres_documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
           ->useDisk('local');
    }

    public function getPieceJointeStatut(string $collectionName): string
    {
        return $this->hasMedia($collectionName) ? 'Déposé' : 'Manquant';
    }

    public function gerant()
    {
        return $this->belongsTo(Gerant::class, 'gerant_id');
    }
    public function formations()
    {
        return $this->hasMany(Formation::class, 'entreprise_id');
    }
    public function participants()
    {
        return $this->hasMany(Participant::class, 'entreprise_id');
    }
    public function dossiersGiac(): HasMany
    {
        return $this->hasMany(DossierGiac::class, 'entreprise_cliente_id');
    }

    public function etudesIngenierieFormation(): HasMany
    {
        return $this->hasMany(EtudeIngenierieFormation::class, 'entreprise_id');
    }

    public function etudesDiagnosticStrategique(): HasMany
    {
        return $this->hasMany(EtudeDiagnosticStrategique::class, 'entreprise_id');
    }

    /**
     * Archive de tous les documents PDF générés pour cette entreprise
     * (Modèle 5, Modèle 6, fiche d'évaluation, GIAC, OFPPT...).
     */
    public function documentsGeneres(): MorphMany
    {
        return $this->morphMany(DocumentGenere::class, 'documentable')
            ->latest('genere_le');
    }
    public function getEnteteImageBase64(): ?string
    {
        return $this->getMediaBase64('entete_page');
    }

    public function getPiedPageImageBase64(): ?string
    {
        return $this->getMediaBase64('pied_page');
    }

    /**
     * Convertit le premier média d'une collection en data URI base64.
     * Remplace l'ancien système basé sur les colonnes string image_entete / image_pied_page.
     */
    protected function getMediaBase64(string $collection): ?string
    {
        $media = $this->getFirstMedia($collection);

        if (! $media) {
            return null;
        }

        if (! file_exists($media->getPath())) {
            return null;
        }

        return 'data:' . $media->mime_type . ';base64,' . base64_encode(
            file_get_contents($media->getPath())
        );
    }

    protected static function booted()
    {
        static::deleting(function ($entreprise) {
            // Bloquer s'il y a des formations ou des participants
            if ($entreprise->formations()->exists() || $entreprise->participants()->exists()) {
                throw new \App\Exceptions\SuppressionBloqueeException(
                    "Suppression impossible : cette entreprise possède des formations actives ou des participants rattachés."
                );
            }
        });
    }
    public function anneesFormations(): array
    {
        return $this->formations()
            ->get()
            ->pluck('date_debut')
            ->filter()
            ->map(fn ($date) => (int) $date->format('Y'))
            ->unique()
            ->sortDesc()
            ->values()
            ->all();
    }
    public function anneeArchive(): ?int
    {
        $date = $this->formations()
            ->orderBy('date_debut')
            ->value('date_debut');

        return $date ? (int) $date->format('Y') : null;
    }

   public function statutDerniereFormation(): ?FormationStatus
{
    return $this->formations()
        ->orderByDesc('date_debut')
        ->value('statut');
}
}
