<?php

namespace App\Services;

use App\Enums\FormationStatus;
use App\Enums\TypeFormation;
use App\Exceptions\DocumentGenerationException;
use App\Models\DossierGiac;
use App\Enums\StatutDossierGiac;
use App\Models\EntrepriseCliente;
use App\Models\EntrepriseFormation;
use App\Models\Formation;
use App\Models\Groupe;
use App\Services\Concerns\PersisteDocumentsGeneres;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;

class DocumentGenerationService
{
    use PersisteDocumentsGeneres;

    /**
     * Génère l'attestation "Modèle 6" (Document D) pour UNE formation
     * donnée, sur un exercice précisé par l'utilisateur.
     *
     * Le document liste les thèmes de cette formation (et non plus
     * l'ensemble des formations terminées de l'entreprise) : le bouton de
     * génération vit donc désormais sur la table des Formations plutôt
     * que sur la fiche entreprise, chaque formation ayant son propre
     * Modèle 6.
     *
     * L'archive reste rattachée à l'EntrepriseCliente (et non à la
     * Formation) afin que l'historique complet d'une entreprise continue
     * d'apparaître au même endroit ; l'identifiant de la formation est
     * conservé dans les métadonnées pour pouvoir filtrer/retrouver le
     * document d'origine.
     *
     * @throws DocumentGenerationException si la formation n'est pas au
     *         statut "Terminée", si le gérant n'est pas renseigné, ou si
     *         la formation ne comporte aucun thème à attester.
     */
    public function generateModele6(Formation $formation, int $annee): array
    {
        $formation->loadMissing(['entrepriseCliente.gerant', 'themes.formateur']);

        $entreprise = $formation->entrepriseCliente;

        if (! $entreprise) {
            throw new DocumentGenerationException(
                "Impossible de générer l'attestation : cette formation n'est rattachée à aucune entreprise cliente."
            );
        }

        if ($formation->statut !== FormationStatus::TERMINEE) {
            throw new DocumentGenerationException(
                "Impossible de générer l'attestation : la formation \"{$formation->intitule}\" n'est pas encore au statut \"Terminée\"."
            );
        }

        if (! $entreprise->gerant) {
            throw new DocumentGenerationException(
                "Impossible de générer l'attestation : aucun gérant n'est renseigné pour l'entreprise {$entreprise->raison_sociale}."
            );
        }

        $themes = $formation->themes;

        if ($themes->isEmpty()) {
            throw new DocumentGenerationException(
                "La formation \"{$formation->intitule}\" ne comporte aucun thème à attester."
            );
        }

        $content = $this->renderFromView('documents.modele6', [
            'entreprise'    => $entreprise,
            'gerant'        => $entreprise->gerant,
            'organisme'     => EntrepriseFormation::current(),
            'formation'     => $formation,
            'annee'         => $annee,
            'themes'        => $themes,
            'dateEdition'   => now(),
            'enteteImage'   => $entreprise->getEnteteImageBase64(),
            'piedPageImage' => $entreprise->getPiedPageImageBase64(),
        ]);

        $filename = sprintf(
            'modele6_%s_%s_%d.pdf',
            Str::slug($entreprise->raison_sociale),
            Str::slug($formation->intitule),
            $annee
        );

        $this->finaliserDocument(
            $entreprise,
            'modele6',
            'remboursement',
            $filename,
            $content,
            ['annee' => $annee, 'formation_id' => $formation->id]
        );

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

        // Rattachée à l'entreprise (comme Modèle 6) pour que l'archive
        // d'une entreprise regroupe tous ses documents au même endroit ;
        // repli sur le groupe si, exceptionnellement, l'entreprise n'a
        // pas pu être résolue.
        $this->finaliserDocument(
            $entreprise ?? $groupe,
            'modele5_fiche_presence',
            'remboursement',
            $filename,
            $content,
            ['groupe_id' => $groupe->id, 'theme_id' => $theme->id]
        );

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
     * Encode en Base64 le logo GIAC (asset statique de l'application),
     * pour respecter la contrainte Dompdf isRemoteEnabled => false
     * (cf. config/documents.php).
     */
    protected function giacLogoBase64(): string
    {
        $path = public_path('images/giac/logo-giac.png');

        if (! is_file($path)) {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
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

        // Rattachée à l'entreprise pour la même raison que Modèle 5
        // ci-dessus (repli sur le groupe uniquement si nécessaire).
        $this->finaliserDocument(
            $entreprise ?? $groupe,
            'fiche_evaluation_synthetique',
            'remboursement',
            $filename,
            $content,
            ['groupe_id' => $groupe->id, 'theme_id' => $theme->id, 'ville' => $ville]
        );

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    /**
     * Génère le dossier complet GIAC (les 7 documents officiels G1 -> G7)
     * pour une entreprise. Chaque document se persiste et s'archive
     * lui-même (voir les méthodes generateB1.../generateG...) ; cette
     * méthode ne fait qu'orchestrer les 7 générations et créer la ligne
     * DossierGiac "maîtresse" qui matérialise le dossier dans son
     * ensemble (module GIAC du cahier des charges).
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

        $folderPath = sprintf(
            '%s/entreprise-%d/giac',
            config('documents.storage_path', 'documents'),
            $entreprise->id
        );

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

        $filename = 'G1_Bulletin_Adhesion.pdf';

        $this->finaliserDocument(
            $entreprise,
            'giac_g1_bulletin_adhesion',
            'giac',
            $filename,
            $content,
            ['annee' => $annee, 'groupe_id' => $groupe?->id]
        );

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }


    public function generateB2BulletinReadhesion(EntrepriseCliente $entreprise): array
    {
        $annee = $entreprise->anneesFormations()[0] ?? now()->year;

        $content = $this->renderFromView('documents.giac.b2_bulletin_readhesion', [
            'entreprise'  => $entreprise,
            'gerant'      => $entreprise->gerant,
            'annee'       => $annee,
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = 'B2_Bulletin_Readhesion.pdf';

        $this->finaliserDocument($entreprise, 'giac_g7_bulletin_readhesion', 'giac', $filename, $content);

        return [
            'filename' => $filename,
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

        /*if ($entreprise->effectif_total === null || $entreprise->effectif_total <= 0) {
            throw new DocumentGenerationException(
                "Impossible de générer la Fiche d'Information (G2) : l'effectif total de l'entreprise {$entreprise->raison_sociale} doit être renseigné."
            );
        }*/

        $content = $this->renderFromView('documents.giac.c_fiche_entreprise', [
            'entreprise'  => $entreprise,
            'gerant'      => $entreprise->gerant,
            'dateEdition' => now(),
        ]);

        $filename = 'G2_Fiche_Entreprise.pdf';

        $this->finaliserDocument($entreprise, 'giac_g2_fiche_entreprise', 'giac', $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    public function generateDFicheTechniqueDiagnostic(EntrepriseCliente $entreprise): array
    {
        $etude = $entreprise->etudesDiagnosticStrategique()->latest()->first();

        if (! $etude) {
            throw new DocumentGenerationException(
                "Impossible de générer la fiche D : aucune étude de diagnostic stratégique n'est renseignée pour l'entreprise {$entreprise->raison_sociale}."
            );
        }

        $content = $this->renderFromView('documents.giac.d_fiche_technique_diagnostic', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'etude'       => $etude,
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = 'D_Fiche_Technique_Diagnostic_Strategique.pdf';

        $this->finaliserDocument($entreprise, 'giac_g6_fiche_diagnostic_strategique', 'giac', $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    public function generateEFicheTechniqueIngenierie(EntrepriseCliente $entreprise): array
    {
        $etude = $entreprise->etudesIngenierieFormation()->latest()->first();

        if (! $etude) {
            throw new DocumentGenerationException(
                "Impossible de générer la fiche E : aucune étude d'ingénierie de formation n'est renseignée pour l'entreprise {$entreprise->raison_sociale}."
            );
        }

        $content = $this->renderFromView('documents.giac.e_fiche_technique_ingenierie', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'etude'       => $etude,
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = 'E_Fiche_Technique_Ingenierie_Formation.pdf';

        $this->finaliserDocument($entreprise, 'giac_g4_fiche_ingenierie_formation', 'giac', $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    public function generateFFicheG3(EntrepriseCliente $entreprise): array
    {
        $content = $this->renderFromView('documents.giac.f_fiche_g3', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = 'F_Fiche_G3.pdf';

        $this->finaliserDocument($entreprise, 'giac_g3_fiche_organisme_conseil', 'giac', $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    protected function determinerTypeFormation(EntrepriseCliente $entreprise, int $annee): TypeFormation
    {
        $types = $entreprise->formations()
            ->whereYear('date_debut', $annee)
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

       /* if (empty($entreprise->ville)) {
            throw new DocumentGenerationException(
                "Impossible de générer la Déclaration sur l'Honneur : la ville de l'entreprise {$entreprise->raison_sociale} n'est pas renseignée."
            );
        }*/

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

        $filename = 'G5_Declaration_Honneur.pdf';

        $this->finaliserDocument(
            $entreprise,
            'giac_g5_declaration_honneur',
            'giac',
            $filename,
            $content,
            ['annee' => $annee, 'type_formation' => $typeFormation->value]
        );

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    public function generateImpressionDossier(DossierGiac $dossier): array
    {
        $entreprise = $dossier->entrepriseCliente;
        $entreprise->loadMissing('gerant', 'documentsGeneres', 'formations');
        $formation = $entreprise->formations()->latest()->first();

        $content = $this->renderFromView('documents.archive-dossier-impression', [
            'dossier'               => $dossier,
            'entreprise'            => $entreprise,
            'formation'             => $formation,
            'documentsGeneres'      => $entreprise->documentsGeneres,
            'autresDocuments'       => $entreprise->getMedia('autres_documents'),
            'autresDocumentsOfppt'  => $formation ? $formation->getMedia('autres_documents_ofppt') : collect(),
            'statutFinancement'     => $entreprise->statut_demande_financement,
            'piecesOfppt'           => EntrepriseCliente::PIECES_JOINTES_OFPPT,
            'dateEdition'           => now(),
        ]);

        $filename = sprintf('dossier_%s.pdf', \Illuminate\Support\Str::slug($entreprise->raison_sociale));

        return ['filename' => $filename, 'content' => $content];
    }

    public function generateFicheAccesClient(EntrepriseCliente $entreprise): array
    {
        $entreprise->loadMissing('gerant');

        if (! $entreprise->gerant) {
            throw new DocumentGenerationException(
                "Impossible de générer la Fiche Client : aucun gérant n'est renseigné pour l'entreprise {$entreprise->raison_sociale}."
            );
        }

        $content = $this->renderFromView('documents.fiche-client-interne', [
            'entreprise'  => $entreprise,
            'gerant'      => $entreprise->gerant,
            'organisme'   => EntrepriseFormation::current(),
            'dateEdition' => now(),
        ]);

        $filename = sprintf('fiche_acces_client_%s.pdf', Str::slug($entreprise->raison_sociale));


        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }
}
