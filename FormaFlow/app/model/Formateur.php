<?php

namespace App\model;

use App\Theme;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'specialite',
        'statut'
    ];

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }
}
