<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class EtudeIngenierieFormation extends Model
{
    protected $table = 'etudes_ingenierie_formation';

    protected $fillable = [
        'entreprise_id',
        'nature_action',
        'diagnostic_besoins',
        'plan_formation',
        'plan_formation_annee',
        'bilan_competence',
        'autres_precisions',
        'resultats_attendus',
        'periode_debut',
        'periode_fin',
        'nb_jours_intervention',
        'cout_action',
        'date_signature',
    ];

    protected $casts = [
        'plan_formation' => 'boolean',
        'periode_debut'  => 'date:Y-m-d',
        'periode_fin'    => 'date:Y-m-d',
        'cout_action'    => 'decimal:2',
        'date_signature' => 'date:Y-m-d',
    ];

    public function entrepriseCliente(): BelongsTo
    {
        return $this->belongsTo(EntrepriseCliente::class, 'entreprise_id');
    }
}
