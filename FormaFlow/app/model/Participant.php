<?php

namespace App\model;

use App\EntrepriseCliente;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(EntrepriseCliente::class, 'entreprise_id');
    }
}
