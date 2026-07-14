<?php

namespace App\Services;

use App\Enums\FormationStatus;
use App\Exceptions\DocumentGenerationException;
use App\Models\EntrepriseCliente;
use App\Models\EntrepriseFormation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Groupe;

/**
 * Point d'entrée unique pour la génération de tous les documents PDF de
 * FormaFlow (attestations, fiches, récapitulatifs, GIAC, ...).
 *
 * Chaque type de document expose sa propre méthode publique (ex.
 * generateModele6) qui prépare les données métier puis délègue le rendu
 * HTML -> PDF aux méthodes protégées communes ci-dessous. Cela évite de
 * dupliquer la configuration Dompdf, la mise en page, ou la logique de
 * sauvegarde à chaque nouveau document.
 */
class DocumentGenerationService
{
    /**
     * Génère l'attestation "Modèle 6" (Document D) pour une entreprise
     * cliente, sur un exercice donné.
     *
     * @throws DocumentGenerationException si le gérant n'est pas renseigné,
     *         ou si aucune formation/aucun thème éligible n'est trouvé.
     */
    public function generateModele6(EntrepriseCliente $entreprise, int $annee): array
    {
        $entreprise->loadMissing('gerant');

        if (! $entreprise->gerant) {
            throw new DocumentGenerationException(
                "Impossible de générer l'attestation : aucun gérant n'est renseigné pour cette entreprise."
            );
        }

        $formations = $entreprise->formations()
            ->with(['themes.formateur'])
            ->where('statut', FormationStatus::TERMINEE)
            ->whereYear('date_fin', $annee)
            ->orderBy('date_fin')
            ->get();

        if ($formations->isEmpty()) {
            throw new DocumentGenerationException(
                "Aucune formation au statut \"Terminée\" n'a été trouvée pour l'entreprise sur l'exercice {$annee}."
            );
        }

        $themes = $formations->flatMap(fn ($formation) => $formation->themes)->values();

        if ($themes->isEmpty()) {
            throw new DocumentGenerationException(
                "Les formations terminées de l'exercice {$annee} ne comportent aucun thème à attester."
            );
        }

        $content = $this->renderFromView('documents.modele6', [
            'entreprise'  => $entreprise,
            'gerant'      => $entreprise->gerant,
            'organisme'   => EntrepriseFormation::current(),
            'annee'       => $annee,
            'themes'      => $themes,
            'dateEdition' => now(),
        ]);

        $filename = sprintf('modele6_%s_%d.pdf', Str::slug($entreprise->raison_sociale), $annee);

        $this->persist($entreprise, $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }
    

    /**
     * Effectue le rendu Blade -> HTML -> PDF via Dompdf.
     *
     * Méthode générique réutilisable par tous les futurs documents
     * (fiche entreprise, Modèle 5, GIAC, récapitulatif de thèmes, ...).
     */
    protected function renderFromView(string $view, array $data = []): string
    {
        $html = view($view, $data)->render();

        $options = new Options();
        $options->set('defaultFont', config('documents.default_font', 'DejaVu Sans'));

        foreach (config('documents.dompdf_options', []) as $key => $value) {
            $options->set($key, $value);
        }

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper(
            config('documents.paper.size', 'a4'),
            config('documents.paper.orientation', 'portrait')
        );
        $dompdf->render();

        return $dompdf->output();
    }
    /**
     * Génère la "Fiche d'évaluation synthétique par groupe".
     *
     * @throws DocumentGenerationException si le groupe ne contient aucun participant.
     */
    public function generateFicheEvaluation(Groupe $groupe, string $ville): array
    {
        $groupe->loadMissing(['theme.formateur', 'theme.formation.entrepriseCliente', 'participants']);

        $theme = $groupe->theme;

        if ($groupe->participants->isEmpty()) {
            throw new DocumentGenerationException(
                "Impossible de générer la fiche : ce groupe ne contient aucun participant."
            );
        }

        $entreprise = $theme->formation->entrepriseCliente ?? null;
        

    
    $content = $this->renderFromView('documents.fiche_evaluation', [
        'groupe'             => $groupe,
        'entreprise'         => $entreprise,
        'organisme'          => EntrepriseFormation::current(),
        'theme'              => $theme,
        'formateur'          => $theme->formateur,
        'ville'              => $ville,
        'nombreParticipants' => $groupe->participants->count(),
        'dateEdition'        => now(),
    ]);
    $filename = sprintf('fiche_evaluation_%s.pdf', Str::slug($groupe->libelle));

    $this->persistGroupeDocument($groupe, $filename, $content);

    return [
        'filename' => $filename,
        'content'  => $content,
    ];
}


    /**
     * Conserve une copie du document généré sur le disque configuré, afin
     * de permettre son suivi/historique par entreprise (module "Formation"
     * du cahier des charges).
     */
    protected function persist(EntrepriseCliente $entreprise, string $filename, string $content): void
    {
        $path = sprintf(
            '%s/entreprise-%d/%s',
            config('documents.storage_path', 'documents'),
            $entreprise->id,
            $filename
        );

        Storage::disk(config('documents.storage_disk', 'local'))->put($path, $content);
    }
    /**
     * Conserve une copie du document généré, pour un groupe (fiche d'évaluation).
     */
    protected function persistGroupeDocument(Groupe $groupe, string $filename, string $content): void
    {
        $path = sprintf(
            '%s/groupe-%d/%s',
            config('documents.storage_path', 'documents'),
            $groupe->id,
            $filename
        );

        Storage::disk(config('documents.storage_disk', 'local'))->put($path, $content);
    }
}
