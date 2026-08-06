<?php

use App\Enums\CategorieSP;
use App\Enums\DemandeFinancementStatus;
use App\Enums\FormateurStatus;
use App\Enums\GerantGender;
use App\Enums\TypeFormation;

// --- TypeFormation ---------------------------------------------------------

it('TypeFormation has the correct label for each case', function (TypeFormation $type, string $expectedLabel) {
    expect($type->getLabel())->toBe($expectedLabel);
})->with([
    [TypeFormation::INGENIERIE, 'Ingénierie de Formation'],
    [TypeFormation::DIAGNOSTIC, 'Diagnostic Stratégique'],
    [TypeFormation::LES_DEUX, "l'Ingénierie et le Diagnostic"],
]);

// --- DemandeFinancementStatus -----------------------------------------------

it('DemandeFinancementStatus has the correct label for each case', function (DemandeFinancementStatus $status, string $expectedLabel) {
    expect($status->getLabel())->toBe($expectedLabel);
})->with([
    [DemandeFinancementStatus::EN_COURS, 'En cours'],
    [DemandeFinancementStatus::ACCEPTEE, 'Acceptée'],
    [DemandeFinancementStatus::REFUSEE, 'Refusée'],
    [DemandeFinancementStatus::ARCHIVEE, 'Archivée'],
]);

// --- FormateurStatus ---------------------------------------------------------
// This one has an extra static helper (toArray()) beyond getLabel() — worth
// its own test since it's used elsewhere for validation `in:` rules most likely.

it('FormateurStatus has the correct label for each case', function (FormateurStatus $status, string $expectedLabel) {
    expect($status->getLabel())->toBe($expectedLabel);
})->with([
    [FormateurStatus::INTERNE, 'INTERNE'],
    [FormateurStatus::EXTERNE, 'EXTERNE'],
]);

it('FormateurStatus::toArray returns all raw values', function () {
    expect(FormateurStatus::toArray())->toBe(['INTERNE', 'EXTERNE']);
});

// --- CategorieSP ---------------------------------------------------------

it('CategorieSP has the correct label for each case', function (CategorieSP $categorie, string $expectedLabel) {
    expect($categorie->getLabel())->toBe($expectedLabel);
})->with([
    [CategorieSP::Cadre, 'C'],
    [CategorieSP::Employe, 'E'],
    [CategorieSP::Ouvrier, 'O'],
]);

// --- GerantGender ---------------------------------------------------------

it('GerantGender has the correct label for each case', function (GerantGender $gender, string $expectedLabel) {
    expect($gender->getLabel())->toBe($expectedLabel);
})->with([
    [GerantGender::Homme, 'Homme'],
    [GerantGender::Femme, 'Femme'],
]);
