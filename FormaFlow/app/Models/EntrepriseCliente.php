<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrepriseCliente extends Model
{
    protected $fillable = [
        'raisonSociale',
        'siegeSocial',
        'dateCreation',
        'statutJuridique',
        'ice',
        'numCnss',
        'rc',
        'if',
        'patente',
        'secteurActivite',
        'activite',
        'regionAffiliationCnss',
        'effectifTotal',
        'telephone',
        'fax',
        'email',
        'contactRef'
    ];

     protected $casts = [
        'dateCreation' => 'date:Y-m-d',
    ];
}
