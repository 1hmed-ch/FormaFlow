<?php

namespace App\Models;

use App\Enums\FormationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_formation_id',
        'intitule',
        'date_debut',
        'date_fin',
        'statut',
        'entreprise_id',
    ];

    protected $casts = [
        "statut" => FormationStatus::class
    ];

    public function entrepriseCliente(): BelongsTo
    {
        return $this->belongsTo(EntrepriseCliente::class, 'entreprise_id');
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class, 'formation_id');
    }
  

    protected static function booted()
{
    static::deleting(function ($formation) {
        // On cherche s'il existe AU MOINS un groupe rattaché
        // à AU MOINS un thème de cette formation
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

