<?php

namespace App\Models;

use App\Enums\StatutDossierGiac;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierGiac extends Model
{
    protected $table = 'dossiers_giac';

    protected $fillable = [
        'entreprise_cliente_id',
        'statut',
        'date_generation',
        'chemin_stockage',
    ];

    protected $casts = [
        'statut' => StatutDossierGiac::class,
        'date_generation' => 'datetime',
    ];

    public function entrepriseCliente(): BelongsTo
    {
        return $this->belongsTo(EntrepriseCliente::class, 'entreprise_cliente_id');
    }
}