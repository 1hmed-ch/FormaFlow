<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Données propres à l'étude de diagnostic stratégique (D.S.) d'un dossier
 * GIAC, utilisées pour générer la fiche G6 "Fiche Technique de l'Etude du
 * Diagnostic Stratégique".
 *
 * Les coordonnées du cabinet-conseil intervenant (nous-mêmes) ne sont pas
 * stockées ici : elles sont lues depuis EntrepriseFormation::current().
 */
class EtudeDiagnosticStrategique extends Model
{
    protected $table = 'etudes_diagnostic_strategique';

    protected $fillable = [
        'entreprise_id',
        'formation_id',
        'projet_marche_export',
        'projet_investissement_techno',
        'projet_mise_aux_normes',
        'projet_autre',
        'projet_autre_precision',
        'objectifs_resultats_attendus',
        'prestations_envisagees',
        'annee_application',
        'duree_intervention_jours',
        'date_demarrage',
        'cout_previsionnel',
        'date_signature',
    ];
    protected $casts = [
        'projet_marche_export'         => 'boolean',
        'projet_investissement_techno' => 'boolean',
        'projet_mise_aux_normes'       => 'boolean',
        'projet_autre'                 => 'boolean',
        'date_demarrage'               => 'date:Y-m-d',
        'cout_previsionnel'            => 'decimal:2',
        'date_signature'               => 'date:Y-m-d',
    ];

    /**
     * Libellés lisibles des cases "Nature du projet de développement",
     * utilisés côté formulaire Filament et côté vue PDF.
     */
    public const NATURE_PROJET_LABELS = [
        'projet_marche_export'         => "Marché d'Exportation",
        'projet_investissement_techno' => 'Investissement Technologique',
        'projet_mise_aux_normes'       => 'Mise aux Normes',
    ];

    public function entrepriseCliente(): BelongsTo
    {
        return $this->belongsTo(EntrepriseCliente::class, 'entreprise_id');
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class, 'formation_id');
    }
}
