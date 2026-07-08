<?php

namespace App\Models;

use App\Enums\CategorieSP;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'cin',
        'email',
        'numero_cnss',
        'fonction_occupee',
        'telephone',
        'categorie_sp',
        'entreprise_id',
    ];

    protected $casts = [
        'categorie_sp' => CategorieSP::class,
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(EntrepriseCliente::class, 'entreprise_id');
    }

    public function groupes(): BelongsToMany
    {
        return $this->belongsToMany(Groupe::class, 'groupe_participant')
            ->withTimestamps();
    }
}
