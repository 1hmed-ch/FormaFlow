<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gerant extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'genre',
        'fonction',
        'cin'
    ];

    public function entrepriseCliente()
    {
        return $this->hasOne(EntrepriseCliente::class, 'gerant_id');
    }
}
