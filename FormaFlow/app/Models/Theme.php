<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Theme extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'intitule',
        'date_debut',
        'date_fin',
        'objectifs',
        'formation_id',
        'formateur_id'
    ];

     protected $casts = [
        'date_debut' => 'date:Y-m-d',
        'date_fin'   => 'date:Y-m-d',
    ];
    /**
     * Un thème appartient à une Formation.
     */
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Un thème est animé par un Formateur.
     */
    public function formateur(): BelongsTo
    {
        return $this->belongsTo(Formateur::class);
    }
    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'theme_id');
    }
}
