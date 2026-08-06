<?php

namespace App\Models;

use App\Enums\GerantGender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gerant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'genre',
        'fonction',
        'cin',
        'email',
        'telephone',
    ];

    protected $casts = [
      'genre' => GerantGender::class,
    ];

    public function entrepriseCliente()
    {
        return $this->hasOne(EntrepriseCliente::class, 'gerant_id');
    }
}
