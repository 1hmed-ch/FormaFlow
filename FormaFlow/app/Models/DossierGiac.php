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
    public function getProgressionArchive(): int
    {
        $entreprise = $this->entrepriseCliente;

        if (! $entreprise) {
            return 0;
        }

        $totalPieces = count(\App\Models\EntrepriseCliente::PIECES_JOINTES); // 7
        $piecesOk = collect(\App\Models\EntrepriseCliente::PIECES_JOINTES)
            ->keys()
            ->filter(fn ($key) => $entreprise->hasMedia($key))
            ->count();

        $totalGiac = 7; // B1, B2, C, D, E, F, G
        $giacOk = $entreprise->documentsGeneres()
            ->where('categorie', 'giac')
            ->distinct('type_document')
            ->count('type_document');

        $total = $totalPieces + $totalGiac;

        return $total > 0 ? (int) round((($piecesOk + $giacOk) / $total) * 100) : 0;
    }
}