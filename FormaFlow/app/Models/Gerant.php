<?php

namespace App\Models;

use App\Enums\gerantGender;
use Illuminate\Database\Eloquent\Model;

class Gerant extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'genre',
        'fonction',
        'cin',
        'email'
    ];

    protected $casts = [
      'genre' => gerantGender::class,
    ];

    public function entrepriseCliente()
    {
        return $this->hasOne(EntrepriseCliente::class, 'gerant_id');
    }
}
