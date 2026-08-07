<?php

use App\Enums\StatutDossierGiac;

it('has exactly two cases', function () {
    expect(StatutDossierGiac::cases())->toHaveCount(2);
});

it('has the expected string value for each case', function () {
    expect(StatutDossierGiac::EnCours->value)->toBe('en_cours')
        ->and(StatutDossierGiac::Signe->value)->toBe('signe');
});

it('returns the correct label for each case', function (StatutDossierGiac $status, string $expectedLabel) {
    expect($status->getLabel())->toBe($expectedLabel);
})->with([
    'en cours' => [StatutDossierGiac::EnCours, 'En cours'],
    'signe'    => [StatutDossierGiac::Signe, 'Signé'],
]);

// This enum implements HasColor for the Filament badge on the DossierGiac
// infolist. Pinning the color down means a future refactor can't silently
// turn a "Signé" badge orange (warning) without a test noticing.
it('returns the correct Filament badge color for each case', function (StatutDossierGiac $status, string $expectedColor) {
    expect($status->getColor())->toBe($expectedColor);
})->with([
    'en cours is warning' => [StatutDossierGiac::EnCours, 'warning'],
    'signe is success'    => [StatutDossierGiac::Signe, 'success'],
]);
