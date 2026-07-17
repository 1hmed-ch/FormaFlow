<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Groupe extends Model
{
    protected $fillable = [
        'libelle',
        'lieu',
        'effectif_max',
        'theme_id',
    ];

   

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'groupe_participant')
            ->withTimestamps();
    }
}
