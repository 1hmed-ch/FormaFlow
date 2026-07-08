<?php

namespace App\Models;

use App\Enums\FormateurStatus;
use App\Models\Theme;
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

    protected $casts = [
        'statut' => FormateurStatus::class,
    ];

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }

}
