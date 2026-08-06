<?php

use App\Enums\StatutDocumentGenere;

it('has exactly three active cases', function () {
    // Note: 'Remplace' is commented out in the enum source right now.
    // If you re-enable it later, this count (and the datasets below) need updating —
    // that's a *good* thing: this test will fail and remind you.
    expect(StatutDocumentGenere::cases())->toHaveCount(3);
});

it('returns the correct label for each case', function (StatutDocumentGenere $statut, string $expectedLabel) {
    expect($statut->getLabel())->toBe($expectedLabel);
})->with([
    [StatutDocumentGenere::Genere, 'Généré'],
    [StatutDocumentGenere::Depose, 'Déposé'],
    [StatutDocumentGenere::Signe, 'Signé'],
]);

it('returns the correct Filament badge color for each case', function (StatutDocumentGenere $statut, string $expectedColor) {
    expect($statut->getColor())->toBe($expectedColor);
})->with([
    [StatutDocumentGenere::Genere, 'violet'],
    [StatutDocumentGenere::Depose, 'warning'],
    [StatutDocumentGenere::Signe, 'teal'],
]);
