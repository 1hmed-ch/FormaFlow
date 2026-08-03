<?php

namespace App\Models;

use App\Enums\StatutDossierGiac;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DossierGiac extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const PIECES_JOINTES = [
        'bulletin_adhesion'     => "Bulletin d'Adhésion",
        'fiche_information_sur_entreprise' => 'Fiche d’information sur l’entreprise',
        'fiche_technique_ingenierie_formation' => 'Fiche technique de l’étude d’ingénierie de formation',
        'declaration_honneur'   => "Déclaration sur l'Honneur",
        'attestation_rib'       => 'Attestation RIB (originale)',
        'statuts_legalises'     => 'Statuts légalisés récents',
        'attestation_acces_csf' => "Attestation d'accès aux CSF (originale)",
        'rc_modele_j'           => 'Registre de Commerce – Modèle J récent',
        'cheque_frais_adhesion' => "Chèque - frais d'adhésion",
    ];

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

    public function registerMediaCollections(): void
    {
        foreach (array_keys(self::PIECES_JOINTES) as $collection) {
            $this->addMediaCollection($collection)
                ->singleFile()
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
        }
    }

    public function getPieceJointeStatut(string $collection): array
    {
        $media = $this->getFirstMedia($collection);

        if (! $media) {
            return [
                'etat' => 'Manquant',
                'media' => null,
                'nom_fichier' => null,
                'date_ajout' => null,
                'date_maj' => null,
            ];
        }

        return [
            'etat' => $this->statut === StatutDossierGiac::Signe ? 'Signé' : 'En attente',
            'media' => $media,
            'nom_fichier' => $media->file_name,
            'date_ajout' => $media->created_at,
            'date_maj' => $media->updated_at,
        ];
    }

    public static function pourEntreprise(EntrepriseCliente $entreprise): self
    {
        return static::firstOrCreate(
            ['entreprise_cliente_id' => $entreprise->id],
            ['statut' => StatutDossierGiac::EnCours]
        );
    }
}
