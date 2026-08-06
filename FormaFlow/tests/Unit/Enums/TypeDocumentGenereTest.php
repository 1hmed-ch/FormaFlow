<?php

use App\Enums\TypeDocumentGenere;

it('has exactly eleven cases', function () {
    expect(TypeDocumentGenere::cases())->toHaveCount(11);
});

it('returns the correct label for each case', function (TypeDocumentGenere $type, string $expectedLabel) {
    expect($type->getLabel())->toBe($expectedLabel);
})->with([
    'modele 6'       => [TypeDocumentGenere::Modele6, 'Modèle 6'],
    'fiche presence' => [TypeDocumentGenere::Modele5FichePresence, 'Fiche de présence'],
    'eval synth'     => [TypeDocumentGenere::FicheEvaluationSynthetique, "Fiche d'évaluation synthétique"],
    'giac g1'        => [TypeDocumentGenere::GiacG1BulletinAdhesion, "Bulletin d'adhésion"],
    'giac g2'        => [TypeDocumentGenere::GiacG2FicheEntreprise, "Fiche d'information entreprise"],
    'giac g3'        => [TypeDocumentGenere::GiacG3FicheOrganismeConseil, 'Fiche G3 organisme de conseil'],
    'giac g4'        => [TypeDocumentGenere::GiacG4FicheIngenierieFormation, 'Fiche technique ingénierie'],
    'giac g5'        => [TypeDocumentGenere::GiacG5DeclarationHonneur, "Déclaration sur l'honneur"],
    'giac g6'        => [TypeDocumentGenere::GiacG6FicheDiagnosticStrategique, 'Fiche technique diagnostic'],
    'giac g7'        => [TypeDocumentGenere::GiacG7BulletinReadhesion, 'Bulletin de ré-adhésion'],
    'ofppt f3'       => [TypeDocumentGenere::F3FicheIdentificationOrganisme, "F3 Fiche d'identification organisme"],
]);

// Your GiacDocumentGenerationService and DocumentGenerationService likely
// branch on these raw ->value strings (e.g. in config/documents.php or a
// match() block). This test protects that wiring from typos.
it('has the expected raw value for each GIAC case', function () {
    expect(TypeDocumentGenere::GiacG3FicheOrganismeConseil->value)->toBe('giac_g3_fiche_organisme_conseil')
        ->and(TypeDocumentGenere::GiacG4FicheIngenierieFormation->value)->toBe('giac_g4_fiche_ingenierie_formation')
        ->and(TypeDocumentGenere::GiacG6FicheDiagnosticStrategique->value)->toBe('giac_g6_fiche_diagnostic_strategique')
        ->and(TypeDocumentGenere::GiacG7BulletinReadhesion->value)->toBe('giac_g7_bulletin_readhesion');
});
