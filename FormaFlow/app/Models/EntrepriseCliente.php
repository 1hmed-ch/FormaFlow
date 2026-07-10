<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EntrepriseCliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'gerant_id',
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

    public function gerant()
    {
        return $this->belongsTo(Gerant::class, 'gerant_id');
    }
    public function formations()
    {
        return $this->hasMany(Formation::class, 'entreprise_id');
    }
    public function participants()
    {
        return $this->hasMany(Participant::class, 'entreprise_id');
    }

  protected static function booted()
{
    static::deleting(function ($entreprise) {
        // Bloquer s'il y a des formations ou des participants
        if ($entreprise->formations()->exists() || $entreprise->participants()->exists()) {
            throw new \App\Exceptions\SuppressionBloqueeException(
                "Suppression impossible : cette entreprise possède des formations actives ou des participants rattachés."
            );
        }
    });
}
}
