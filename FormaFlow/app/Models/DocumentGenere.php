<?php

namespace App\Models;

use App\Enums\CategorieDocumentGenere;
use App\Enums\StatutDocumentGenere;
use App\Enums\TypeDocumentGenere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Formation;
use App\Models\Theme;
use App\Models\Groupe;

/**
 * Ligne d'archive pour un document PDF généré par la plateforme (Modèle 5,
 * Modèle 6, fiche d'évaluation, documents GIAC/OFPPT...).
 *
 * Rattachée de façon polymorphe (documentable) à l'entité concernée — en
 * pratique presque toujours une EntrepriseCliente, afin que l'ensemble des
 * documents d'un dossier soit consultable au même endroit (modules
 * "Formation" et "Archives" du cahier des charges).
 *
 * Une nouvelle ligne est créée à chaque génération (jamais de mise à jour) :
 * `version` permet de retrouver l'historique complet d'un même type de
 * document pour une même entité, plutôt que d'écraser le précédent.
 */
class DocumentGenere extends Model
{
    protected $table = 'documents_generes';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'type_document',
        'categorie',
        'nom_fichier',
        'disque',
        'chemin',
        'taille',
        'version',
        'statut',
        'genere_par',
        'genere_le',
        'metadonnees',
    ];

    protected $casts = [
        'type_document' => TypeDocumentGenere::class,
        'categorie' => CategorieDocumentGenere::class,
        'statut' => StatutDocumentGenere::class,
        'genere_le' => 'datetime',
        'metadonnees' => 'array',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Résout la Formation d'origine d'un document "remboursement" à partir
     * des métadonnées conservées à la génération (formation_id pour le
     * Modèle 6, theme_id/groupe_id pour le Modèle 5 et la fiche
     * d'évaluation, générés par groupe). Retourne null pour les documents
     * GIAC/OFPPT, qui ne sont pas rattachés à une formation précise.
     */
    public function formationLiee(): ?Formation
    {
        $meta = $this->metadonnees ?? [];

        if (! empty($meta['formation_id'])) {
            return Formation::find($meta['formation_id']);
        }

        if (! empty($meta['theme_id'])) {
            return Theme::find($meta['theme_id'])?->formation;
        }

        if (! empty($meta['groupe_id'])) {
            return Groupe::find($meta['groupe_id'])?->theme?->formation;
        }

        return null;
    }

    public function generePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'genere_par');
    }

    /**
     * Vérifie que le fichier existe toujours physiquement sur le disque
     * (défensif : un fichier peut avoir été déplacé/purgé manuellement
     * sans que la ligne d'archive ne soit supprimée).
     */
    public function existeSurLeDisque(): bool
    {
        return Storage::disk($this->disque)->exists($this->chemin);
    }

    /**
     * Taille lisible pour l'affichage (ex. "482 Ko").
     */
    public function tailleLisible(): string
    {
        if (! $this->taille) {
            return '—';
        }

        $unites = ['o', 'Ko', 'Mo', 'Go'];
        $taille = (float) $this->taille;
        $i = 0;

        while ($taille >= 1024 && $i < count($unites) - 1) {
            $taille /= 1024;
            $i++;
        }

        return round($taille, 1) . ' ' . $unites[$i];
    }
}
