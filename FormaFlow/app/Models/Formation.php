<?php

namespace App\Models;

use App\Enums\FormationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\TypeFormation;


class Formation extends Model implements HasMedia
{
     use HasFactory, InteractsWithMedia;

    public const PIECES_JOINTES_CABINET = [
        'fiche_identification' => 'Fiche d’identification de l’organisme de formation',
        'fiche_renseignement'  => 'Fiche de renseignement de l’organisme de conseil',
    ];

    protected $fillable = [
        'entreprise_formation_id',
        'intitule',
        'date_debut',
        'date_fin',
        'type_formation',
        'statut',
        'entreprise_id',
    ];

    protected $casts = [
        "statut" => FormationStatus::class,
        'type_formation' => TypeFormation::class,
        'date_debut' => 'date:Y-m-d',
        'date_fin' => 'date:Y-m-d',
    ];
     public function registerMediaCollections(): void
    {
        // Checklist GIAC signée par l'entreprise
        foreach (array_keys(DossierGiac::PIECES_JOINTES) as $collection) {
            $this->addMediaCollection($collection)
                ->singleFile()
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
        }

        // Pièces du cabinet — désormais propres à CHAQUE formation
        foreach (array_keys(self::PIECES_JOINTES_CABINET) as $collection) {
            $this->addMediaCollection($collection)
                ->singleFile()
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
        }

        // Financement OFPPT
        foreach (array_keys(EntrepriseCliente::PIECES_JOINTES_OFPPT) as $collection) {
            $this->addMediaCollection($collection)
                ->singleFile()
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->useDisk('local');
        }

        foreach (['eligibilite_csf', 'facture_pro_forma'] as $collection) {
            $this->addMediaCollection($collection)
                ->singleFile()
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->useDisk('local');
        }

        foreach ([
            'autres_documents_formation',
            'autres_documents_signes_entreprise',
            'autres_documents_signes_cabinet',
            'autres_documents_ofppt',
        ] as $collection) {
            $this->addMediaCollection($collection)
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->useDisk('local');
        }
    }
    public function getPieceJointeStatut(string $collection): array
    {
        $media = $this->getFirstMedia($collection);

        if (! $media) {
            return ['etat' => 'Manquant', 'media' => null, 'nom_fichier' => null, 'date_ajout' => null, 'date_maj' => null];
        }

        return [
            'etat' => 'Déposé',
            'media' => $media,
            'nom_fichier' => $media->file_name,
            'date_ajout' => $media->created_at,
            'date_maj' => $media->updated_at,
        ];
    }
    
    public function entrepriseCliente(): BelongsTo
    {
        return $this->belongsTo(EntrepriseCliente::class, 'entreprise_id');
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class, 'formation_id');
    }

    public function etudeIngenierieFormation(): HasOne
    {
        return $this->hasOne(EtudeIngenierieFormation::class, 'formation_id');
    }

    public function etudeDiagnosticStrategique(): HasOne
    {
        return $this->hasOne(EtudeDiagnosticStrategique::class, 'formation_id');
    }

    /**
     * Ne garde que les formations au statut "Terminée".
     */
    public function scopeTerminees(Builder $query): Builder
    {
        return $query->where('statut', FormationStatus::TERMINEE);
    }

    /**
     * Restreint aux formations dont la période [date_debut, date_fin] chevauche
     * [$debut, $fin]. Les deux bornes sont optionnelles.
     */
    public function scopeDansPeriode(Builder $query, ?string $debut, ?string $fin): Builder
    {
        return $query
            ->when($debut, fn (Builder $q, string $date) => $q->where('date_fin', '>=', $date))
            ->when($fin, fn (Builder $q, string $date) => $q->where('date_debut', '<=', $date));
    }

    protected static function booted(){
    static::created(function (Formation $formation) {
        if ($formation->entrepriseCliente) {
            DossierGiac::pourEntreprise($formation->entrepriseCliente);
        }
    });

    static::deleting(function ($formation) {
        $aDesGroupesActifs = $formation->themes()
            ->whereHas('groupes')
            ->exists();

            if ($aDesGroupesActifs) {
                throw new \App\Exceptions\SuppressionBloqueeException(
                    "Suppression impossible : cette formation contient des thèmes ayant des groupes actifs."
                );
            }
        });
    }
}
