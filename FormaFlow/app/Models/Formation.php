<?php

namespace App\Models;

use App\Enums\FormationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\TypeFormation;

class Formation extends Model
{
    use HasFactory;

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
