<?php

namespace App\Services;

use App\Enums\FormationStatus;
use App\Exceptions\DocumentGenerationException;
use App\Models\EntrepriseCliente;
use App\Models\EntrepriseFormation;
use App\Models\Groupe;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DossierGiac;
use App\Enums\StatutDossierGiac;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\TypeFormation;
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
            ->where('statut', FormationStatus::TERMINEE)
            ->with(['themes.formateur'])
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
            'entreprise'    => $entreprise,
            'gerant'        => $entreprise->gerant,
            'organisme'     => EntrepriseFormation::current(),
            'annee'         => $annee,
            'themes'        => $themes,
            'dateEdition'   => now(),
            'enteteImage'   => $entreprise->getEnteteImageBase64(),
            'piedPageImage' => $entreprise->getPiedPageImageBase64(),
        ]);

        $filename = sprintf('modele6_%s_%d.pdf', Str::slug($entreprise->raison_sociale), $annee);

        $this->persist($entreprise, $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }


    /**
     * Génère la "Fiche de présence" (Modèle 5) pour un groupe donné.
     *
     * Générée par groupe et non par thème : un thème peut être dispensé à
     * plusieurs groupes (sessions distinctes), chacun avec ses propres
     * participants, donc sa propre feuille de présence à émarger.
     *
     * @throws DocumentGenerationException si le groupe n'a aucun participant.
     */
    public function generateFichePresence(Groupe $groupe): array
    {
        $groupe->loadMissing(['theme.formation.entrepriseCliente', 'theme.formateur', 'participants']);

        $theme = $groupe->theme;
        $formation = $theme->formation;
        $entreprise = $formation->entrepriseCliente;

        if ($groupe->participants->isEmpty()) {
            throw new DocumentGenerationException(
                "Impossible de générer la fiche de présence : aucun participant n'est rattaché à ce groupe."
            );
        }

        $content = $this->renderFromView('documents.fiche-presence', [
            'groupe'        => $groupe,
            'theme'         => $theme,
            'entreprise'    => $entreprise,
            'formateur'     => $theme->formateur,
            'participants'  => $groupe->participants,
            'organisme'     => EntrepriseFormation::current(),
            'dateEdition'   => now(),
            'enteteImage'   => $entreprise?->getEnteteImageBase64(),
            'piedPageImage' => $entreprise?->getPiedPageImageBase64(),
        ]);

        $filename = sprintf('fiche_presence_%s_%d.pdf', Str::slug($groupe->libelle), $groupe->id);

        $this->persist($entreprise, $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

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

/**
 * Génère le dossier complet GIAC (les 7 documents officiels B1 -> G) pour une entreprise.
 *
 * @param EntrepriseCliente $entreprise
 * @return array Array d'objets PDF ['filename' => string, 'content' => string]
 */
   
    public function generateDossierGiac(EntrepriseCliente $entreprise): array
    {
        $documents = [
            'B1' => $this->generateB1BulletinAdhesion($entreprise),
            'B2' => $this->generateB2BulletinReadhesion($entreprise),
            'C'  => $this->generateCFicheEntreprise($entreprise),
            'D'  => $this->generateDFicheTechniqueDiagnostic($entreprise),
            'E'  => $this->generateEFicheTechniqueIngenierie($entreprise),
            'F'  => $this->generateFFicheG3($entreprise),
            'G'  => $this->generateGDeclarationHonneur($entreprise),
        ];

        // Chemin de persistance : storage/app/documents/entreprise-{id}/giac/
        $folderPath = sprintf(
            '%s/entreprise-%d/giac',
            config('documents.storage_path', 'documents'),
            $entreprise->id
        );

        $disk = Storage::disk(config('documents.storage_disk', 'local'));

        // Stocker physiquement les 7 fichiers PDF
        foreach ($documents as $doc) {
            $disk->put("{$folderPath}/{$doc['filename']}", $doc['content']);
        }

        // Créer le dossier GIAC en BDD
        DossierGiac::create([
            'entreprise_cliente_id' => $entreprise->id,
            'statut'                => StatutDossierGiac::EnCours,
            'date_generation'       => now(),
            'chemin_stockage'       => $folderPath,
        ]);

        return $documents;
    }

    public function generateB1BulletinAdhesion(EntrepriseCliente $entreprise, ?Groupe $groupe = null): array
{
    $entreprise->loadMissing(['gerant', 'formations.themes.groupes']);

    if (! $entreprise->gerant) {
        throw new DocumentGenerationException(
            "Impossible de générer le Bulletin d'Adhésion (G1) : aucun gérant n'est renseigné pour l'entreprise {$entreprise->raison_sociale}."
        );
    }

    $theme = $groupe?->theme ?? $entreprise->formations
        ->flatMap(fn ($f) => $f->themes)
        ->first();

    $dateDemande = $theme?->date_debut ?? $theme?->date_fin ?? now();
    $annee       = $dateDemande->year;
    $villeFinale = $groupe?->lieu ?? $entreprise->ville ?? 'Fès';

    $content = $this->renderFromView('documents.giac.b1_bulletin_adhesion', [
        'entreprise'  => $entreprise,
        'gerant'      => $entreprise->gerant,
        'annee'       => $annee,
        'ville'       => $villeFinale,
        'dateEdition' => $dateDemande, 
    ]);

    return [
        'filename' => 'G1_Bulletin_Adhesion.pdf',
        'content'  => $content,
    ];
}


    public function generateB2BulletinReadhesion(EntrepriseCliente $entreprise): array
    {
        $content = $this->renderFromView('documents.giac.b2_bulletin_readhesion', [
            'entreprise'  => $entreprise,
            'dateEdition' => now(),
        ]);

        return [
            'filename' => 'B2_Bulletin_Readhesion.pdf',
            'content'  => $content,
        ];
    }

   public function generateCFicheEntreprise(EntrepriseCliente $entreprise): array
    {
        $entreprise->loadMissing('gerant');

        if (! $entreprise->gerant) {
            throw new DocumentGenerationException(
                "Impossible de générer la Fiche d'Information (G2) : aucun gérant n'est renseigné pour l'entreprise {$entreprise->raison_sociale}."
            );
        }

        if ($entreprise->effectif_total === null || $entreprise->effectif_total <= 0) {
            throw new DocumentGenerationException(
                "Impossible de générer la Fiche d'Information (G2) : l'effectif total de l'entreprise {$entreprise->raison_sociale} doit être renseigné."
            );
        }

        $content = $this->renderFromView('documents.giac.c_fiche_entreprise', [
            'entreprise'  => $entreprise,
            'gerant'      => $entreprise->gerant,
            'dateEdition' => now(),
        ]);

        return [
            'filename' => 'G2_Fiche_Entreprise.pdf',
            'content'  => $content,
        ];
    }   
    public function generateDFicheTechniqueDiagnostic(EntrepriseCliente $entreprise): array
    {
        $content = $this->renderFromView('documents.giac.d_fiche_technique_diagnostic', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'dateEdition' => now(),
        ]);

        return [
            'filename' => 'D_Fiche_Technique_Diagnostic_Strategique.pdf',
            'content'  => $content,
        ];
    }

    public function generateEFicheTechniqueIngenierie(EntrepriseCliente $entreprise): array
    {
        $content = $this->renderFromView('documents.giac.e_fiche_technique_ingenierie', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'dateEdition' => now(),
        ]);

        return [
            'filename' => 'E_Fiche_Technique_Ingenierie_Formation.pdf',
            'content'  => $content,
        ];
    }
    
    public function generateFFicheG3(EntrepriseCliente $entreprise): array
    {
        $content = $this->renderFromView('documents.giac.f_fiche_g3', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'dateEdition' => now(),
        ]);

        return [
            'filename' => 'F_Fiche_G3.pdf',
            'content'  => $content,
        ];
    }

 protected function determinerTypeFormation(EntrepriseCliente $entreprise, int $annee): TypeFormation
    {
        $types = $entreprise->formations()
            ->whereHas('themes', fn ($q) => $q->whereYear('date_fin', $annee))
            ->pluck('type_formation')
            ->unique();

        if ($types->isEmpty()) {
            throw new DocumentGenerationException(
                "Aucune formation trouvée pour l'entreprise {$entreprise->raison_sociale} sur l'année {$annee}."
            );
        }

        return $types->count() > 1 ? TypeFormation::LES_DEUX : $types->first();
    }
    
    
    public function generateGDeclarationHonneur(EntrepriseCliente $entreprise): array
    {
        $entreprise->loadMissing('gerant');

        if (! $entreprise->gerant) {
            throw new DocumentGenerationException(
                "Impossible de générer la Déclaration sur l'Honneur  : aucun gérant n'est renseigné pour l'entreprise {$entreprise->raison_sociale}."
            );
        }

        if (empty($entreprise->ville)) {
            throw new DocumentGenerationException(
                "Impossible de générer la Déclaration sur l'Honneur : la ville de l'entreprise {$entreprise->raison_sociale} n'est pas renseignée."
            );
        }

        $annees = $entreprise->anneesFormations();

        if (empty($annees)) {
            throw new DocumentGenerationException(
                "Impossible de générer la Déclaration sur l'Honneur : aucune formation trouvée pour l'entreprise {$entreprise->raison_sociale}."
            );
        }

        $annee         = $annees[0];
        $typeFormation = $this->determinerTypeFormation($entreprise, $annee);

        $content = $this->renderFromView('documents.giac.g_declaration_honneur', [
            'entreprise'    => $entreprise,
            'gerant'        => $entreprise->gerant,
            'typeFormation' => $typeFormation,
            'annee'         => $annee,
            'ville'         => $entreprise->ville,
            'dateEdition'   => now(),
            'enteteImage'   => $entreprise->getEnteteImageBase64(),
            'piedPageImage' => $entreprise->getPiedPageImageBase64(),
        ]);

        return [
            'filename' => 'G5_Declaration_Honneur.pdf',
            'content'  => $content,
        ];
    }
}
