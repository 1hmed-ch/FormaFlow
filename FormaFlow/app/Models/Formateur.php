<?php

namespace App\Models;

use App\Enums\FormateurStatus;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Formateur extends Model
{
    protected $fillable = [
        'entreprise_formation_id',
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

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => "{$attributes['nom']} {$attributes['prenom']}",
        );
    }

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }
    public function organisme(): BelongsTo
    {
        return $this->belongsTo(EntrepriseFormation::class, 'entreprise_formation_id');
    }
}
