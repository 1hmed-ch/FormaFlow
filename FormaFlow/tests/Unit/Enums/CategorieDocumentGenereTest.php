<?php

use App\Enums\CategorieDocumentGenere;

it('has exactly four cases', function () {
    expect(CategorieDocumentGenere::cases())->toHaveCount(4);
});

it('returns the correct label for each case', function (CategorieDocumentGenere $categorie, string $expectedLabel) {
    expect($categorie->getLabel())->toBe($expectedLabel);
})->with([
    [CategorieDocumentGenere::Remboursement, 'Remboursement'],
    [CategorieDocumentGenere::Giac, 'GIAC'],
    [CategorieDocumentGenere::Ofppt, 'OFPPT'],
    [CategorieDocumentGenere::Entreprise, 'Entreprise'],
]);

it('returns the correct Filament badge color for each case', function (CategorieDocumentGenere $categorie, string $expectedColor) {
    expect($categorie->getColor())->toBe($expectedColor);
})->with([
    [CategorieDocumentGenere::Remboursement, 'info'],
    [CategorieDocumentGenere::Giac, 'warning'],
    [CategorieDocumentGenere::Ofppt, 'success'],
    [CategorieDocumentGenere::Entreprise, 'gray'],
]);
