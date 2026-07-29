<?php

namespace App\Services;

use App\Exceptions\DocumentGenerationException;
use App\Models\EntrepriseCliente;
use App\Models\EntrepriseFormation;
use App\Models\EtudeDiagnosticStrategique;
use App\Models\EtudeIngenierieFormation;
use App\Services\Concerns\PersisteDocumentsGeneres;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;

/**
 * Génère le volet "Organisme de Formation / Cabinet" du Dossier GIAC
 * (TICKET-GIAC-3) :
 *  - G3 : Fiche de Renseignement de l'Organisme de Conseil
 *  - G4 : Fiche Technique de l'Etude d'Ingénierie de Formation
 *  - G6 : Fiche Technique de l'Etude du Diagnostic Stratégique
 *  - G7 : Bulletin de Ré-adhésion et de Frais de dossier
 *
 * Contient également, par commodité d'infrastructure partagée :
 *  - Formulaire F3 (OFPPT/CSF) : Fiche d'identification de l'Organisme de
 *    Formation — un document officiel différent de G3, voir la méthode
 *    generateF3FicheIdentificationOrganisme() pour le détail.
 *
 * Volontairement séparé de DocumentGenerationService (qui gère Modèle 5,
 * Modèle 6 et le volet Entreprise du dossier GIAC — G1/G2/G5, TICKET-GIAC-2)
 * afin d'éviter les conflits de fusion entre les deux tickets menés en
 * parallèle. La persistance (finaliserDocument()) est désormais mutualisée
 * via App\Services\Concerns\PersisteDocumentsGeneres ; renderFromView()
 * reste dupliqué entre les deux services (refactor à part, sans lien avec
 * l'archive des documents générés).
 *
 * Contrairement à Modèle 5/6, ces documents sont des formulaires officiels
 * du GIAC : leur en-tête est le logo GIAC (asset statique de l'application,
 * public/images/giac/logo-giac.png), et non l'entête/pied de page propres
 * au client (getEnteteImageBase64() / getPiedPageImageBase64()) — ce
 * mécanisme reste réservé à Modèle 5 et Modèle 6.
 */
class GiacDocumentGenerationService
{
    use PersisteDocumentsGeneres;

    /**
     * Génère G3 - Fiche de Renseignement de l'Organisme de Conseil.
     *
     * Contenu entièrement dérivé de l'organisme de formation courant :
     * ce document est identique pour tous les dossiers, mais est généré
     * et archivé par entreprise cliente car il fait partie intégrante de
     * chaque dossier GIAC déposé.
     */
    public function generateFicheOrganismeConseil(EntrepriseCliente $entreprise): array
    {
        $organisme = EntrepriseFormation::current();

        $content = $this->renderFromView('documents.giac.f_fiche_g3', [
            'organisme'   => $organisme,
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = 'giac_g3_fiche_organisme_conseil.pdf';

        $this->finaliserDocument($entreprise, 'giac_g3_fiche_organisme_conseil', 'giac', $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    /**
     * Génère G4 - Fiche Technique de l'Etude d'Ingénierie de Formation.
     *
     * @throws DocumentGenerationException si l'étude fournie ne concerne
     *         pas l'entreprise donnée.
     */
    public function generateFicheIngenierieFormation(
        EntrepriseCliente $entreprise,
        EtudeIngenierieFormation $etude
    ): array {
        $this->assertEtudeAppartientAEntreprise($etude->entreprise_id, $entreprise);

        $content = $this->renderFromView('documents.giac.e_fiche_technique_ingenierie', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'etude'       => $etude,
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = sprintf('giac_g4_fiche_ingenierie_formation_%s.pdf', Str::slug($entreprise->raison_sociale));

        $this->finaliserDocument(
            $entreprise,
            'giac_g4_fiche_ingenierie_formation',
            'giac',
            $filename,
            $content,
            ['etude_id' => $etude->id]
        );

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    /**
     * Génère G6 - Fiche Technique de l'Etude du Diagnostic Stratégique.
     *
     * @throws DocumentGenerationException si l'étude fournie ne concerne
     *         pas l'entreprise donnée.
     */
    public function generateFicheDiagnosticStrategique(
        EntrepriseCliente $entreprise,
        EtudeDiagnosticStrategique $etude
    ): array {
        $this->assertEtudeAppartientAEntreprise($etude->entreprise_id, $entreprise);

        $content = $this->renderFromView('documents.giac.d_fiche_technique_diagnostic', [
            'entreprise'  => $entreprise,
            'organisme'   => EntrepriseFormation::current(),
            'etude'       => $etude,
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = sprintf('giac_g6_fiche_diagnostic_strategique_%s.pdf', Str::slug($entreprise->raison_sociale));

        $this->finaliserDocument(
            $entreprise,
            'giac_g6_fiche_diagnostic_strategique',
            'giac',
            $filename,
            $content,
            ['etude_id' => $etude->id]
        );

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    /**
     * Génère G7 - Bulletin de Ré-adhésion et de Frais de dossier.
     *
     * @throws DocumentGenerationException si le gérant n'est pas renseigné
     *         pour cette entreprise (il doit signer le bulletin).
     */
    public function generateBulletinReadhesion(EntrepriseCliente $entreprise, int $annee): array
    {
        $entreprise->loadMissing('gerant');

        if (! $entreprise->gerant) {
            throw new DocumentGenerationException(
                "Impossible de générer le bulletin de ré-adhésion : aucun gérant n'est renseigné pour cette entreprise."
            );
        }

        $content = $this->renderFromView('documents.giac.b2_bulletin_readhesion', [
            'entreprise'  => $entreprise,
            'gerant'      => $entreprise->gerant,
            'annee'       => $annee,
            'giacLogo'    => $this->giacLogoBase64(),
            'dateEdition' => now(),
        ]);

        $filename = sprintf('giac_b2_bulletin_readhesion_%s_%d.pdf', Str::slug($entreprise->raison_sociale), $annee);

        $this->finaliserDocument(
            $entreprise,
            'giac_g7_bulletin_readhesion',
            'giac',
            $filename,
            $content,
            ['annee' => $annee]
        );

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    /**
     * Génère le Formulaire F3 OFPPT - Fiche d'identification de l'Organisme
     * de Formation (https://csf.ofppt.org.ma).
     *
     * Document officiel distinct de G3 (Fiche de Renseignement de
     * l'Organisme de Conseil, propre au GIAC) : mêmes données sources
     * (EntrepriseFormation::current()) mais mise en page et institution
     * différentes. Conservé ici par simplicité (même infrastructure de
     * rendu/persistance) même s'il appartient, à proprement parler, au
     * module OFPPT plutôt qu'au Dossier GIAC — à déplacer si un service
     * dédié au module OFPPT voit le jour.
     */
    public function generateF3FicheIdentificationOrganisme(EntrepriseCliente $entreprise): array
    {
        $organisme = EntrepriseFormation::current();

        $content = $this->renderFromView('documents.giac.f3_fiche_organisme_formation', [
            'organisme'   => $organisme,
            'dateEdition' => now(),
        ]);

        $filename = 'f3_fiche_identification_organisme.pdf';

        $this->finaliserDocument($entreprise, 'f3_fiche_identification_organisme', 'ofppt', $filename, $content);

        return [
            'filename' => $filename,
            'content'  => $content,
        ];
    }

    /**
     * Garde-fou : évite de générer une fiche G4/G6 avec l'étude d'une autre
     * entreprise par erreur d'appel côté contrôleur/action Filament.
     */
    protected function assertEtudeAppartientAEntreprise(int $etudeEntrepriseId, EntrepriseCliente $entreprise): void
    {
        if ($etudeEntrepriseId !== $entreprise->id) {
            throw new DocumentGenerationException(
                "L'étude fournie ne correspond pas à l'entreprise {$entreprise->raison_sociale}."
            );
        }
    }

    /**
     * Encode en Base64 le logo GIAC (asset statique de l'application),
     * pour respecter la contrainte Dompdf isRemoteEnabled => false
     * (cf. config/documents.php).
     */
    protected function giacLogoBase64(): string
    {
        $path = public_path('images/giac/logo-giac.png');

        // Défensif : tant que l'asset réel n'est pas déposé dans
        // public/images/giac/logo-giac.png, on évite un warning PHP
        // (file_get_contents sur un fichier absent) et un PDF corrompu.
        // Le tag <img src="{{ $giacLogo }}"> se contente de ne rien
        // afficher si la chaîne est vide.
        if (! is_file($path)) {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
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
}
