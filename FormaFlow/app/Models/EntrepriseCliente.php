<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrepriseCliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'raison_sociale',
        'siege_social',
        'date_creation',
        'statut_juridique',
        'ice',
        'num_cnss',
        'rc',
        'if',
        'patente',
        'secteur_activite',
        'activite',
        'region_affiliation_cnss',
        'effectif_total',
        'telephone',
        'fax',
        'email',
        'contact_ref'
    ];

     protected $casts = [
        'date_creation' => 'date:Y-m-d',
    ];
}
