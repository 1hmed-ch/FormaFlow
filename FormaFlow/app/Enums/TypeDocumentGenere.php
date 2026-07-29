<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TypeDocumentGenere: string implements HasLabel
{
    case Modele6 = 'modele6';
    case Modele5FichePresence = 'modele5_fiche_presence';
    case FicheEvaluationSynthetique = 'fiche_evaluation_synthetique';

    case GiacG1BulletinAdhesion = 'giac_g1_bulletin_adhesion';
    case GiacG2FicheEntreprise = 'giac_g2_fiche_entreprise';
    case GiacG3FicheOrganismeConseil = 'giac_g3_fiche_organisme_conseil';
    case GiacG4FicheIngenierieFormation = 'giac_g4_fiche_ingenierie_formation';
    case GiacG5DeclarationHonneur = 'giac_g5_declaration_honneur';
    case GiacG6FicheDiagnosticStrategique = 'giac_g6_fiche_diagnostic_strategique';
    case GiacG7BulletinReadhesion = 'giac_g7_bulletin_readhesion';

    case F3FicheIdentificationOrganisme = 'f3_fiche_identification_organisme';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Modele6 => 'Modèle 6 — Attestation',
            self::Modele5FichePresence => 'Modèle 5 — Fiche de présence',
            self::FicheEvaluationSynthetique => "Fiche d'évaluation synthétique",
            self::GiacG1BulletinAdhesion => "B1 — Bulletin d'adhésion",
            self::GiacG2FicheEntreprise => 'C — Fiche d\'information entreprise',
            self::GiacG3FicheOrganismeConseil => 'F — Fiche G3 organisme de conseil',
            self::GiacG4FicheIngenierieFormation => 'E — Fiche technique ingénierie',
            self::GiacG5DeclarationHonneur => "G — Déclaration sur l'honneur",
            self::GiacG6FicheDiagnosticStrategique => 'D — Fiche technique diagnostic',
            self::GiacG7BulletinReadhesion => 'B2 — Bulletin de ré-adhésion',
            self::F3FicheIdentificationOrganisme => "F3 — Fiche d'identification organisme",
        };
    }
}
