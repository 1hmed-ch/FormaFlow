<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;


class EntrepriseCliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'gerant_id',
        'raison_sociale',
        'siege_social',
        'ville',
        'date_creation',
        'statut_juridique',
        'ice',
        'num_cnss',
        'montant_tfp',
        'deja_depose_giac',
        'nom_ancien_giac',
        'date_depot_ancien_giac',
        'rc',
        'if',
        'patente',
        'secteur_activite',
        'activite',
        'region_affiliation_cnss',
        'effectif_total',
        'effectif_cadre',
        'effectif_cadre_moyen',
        'effectif_agent_qualifie',
        'effectif_agent_sans_qualification',
        'effectif_agent_occasionnel',
        'telephone',
        'fax',
        'email',
        'contact_ref',
        'image_entete',
        'image_pied_page',
    ];

    protected $casts = [
        'date_creation' => 'date:Y-m-d',
        'deja_depose_giac' => 'boolean',
        'date_depot_ancien_giac' => 'date:Y-m-d',
        'montant_tfp' => 'decimal:2',
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
    public function dossiersGiac(): HasMany
    {
        return $this->hasMany(DossierGiac::class, 'entreprise_cliente_id');
    }

    public function etudesIngenierieFormation(): HasMany
    {
        return $this->hasMany(EtudeIngenierieFormation::class, 'entreprise_id');
    }

    public function etudesDiagnosticStrategique(): HasMany
    {
        return $this->hasMany(EtudeDiagnosticStrategique::class, 'entreprise_id');
    }

    /**
     * Archive de tous les documents PDF générés pour cette entreprise
     * (Modèle 5, Modèle 6, fiche d'évaluation, GIAC, OFPPT...).
     */
    public function documentsGeneres(): MorphMany
    {
        return $this->morphMany(DocumentGenere::class, 'documentable')
            ->latest('genere_le');
    }
    public function getEnteteImageBase64(): ?string
    {
        return $this->fileToBase64DataUri($this->image_entete);
    }

    public function getPiedPageImageBase64(): ?string
    {
        return $this->fileToBase64DataUri($this->image_pied_page);
    }

    protected function fileToBase64DataUri(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $disk = config('filament.default_filesystem_disk', 'local');

        if (! Storage::disk($disk)->exists($path)) {
            return null;
        }

        $mimeType = Storage::disk($disk)->mimeType($path) ?: 'image/png';
        $contents = Storage::disk($disk)->get($path);

        return 'data:' . $mimeType . ';base64,' . base64_encode($contents);
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
    public function anneesFormations(): array
    {
        return $this->formations()
            ->with('themes')
            ->get()
            ->flatMap(fn ($formation) => $formation->themes)
            ->pluck('date_fin')
            ->filter()
            ->map(fn ($date) => (int) $date->format('Y'))
            ->unique()
            ->sortDesc()
            ->values()
            ->all();
    }
}
