<?php

use App\Enums\FormationStatus;

// "it(...)" is one test. The string is just a human-readable description —
// it shows up in your terminal output exactly as written, so make it read
// like a sentence: "it has four cases".
it('has exactly four cases', function () {
    expect(FormationStatus::cases())->toHaveCount(4);
});

// Backed enums (enum X: string) store a raw value behind each case.
// This locks in the *exact* strings your DB columns / Filament forms rely on —
// if someone accidentally renames 'Terminée' to 'Termine' in the enum,
// this test fails immediately instead of silently breaking every "Terminée"
// filter/query in the app.
it('has the expected string value for each case', function () {
    expect(FormationStatus::PLANIFIEE->value)->toBe('Planifiée')
        ->and(FormationStatus::EN_COURS->value)->toBe('En cours')
        ->and(FormationStatus::TERMINEE->value)->toBe('Terminée')
        ->and(FormationStatus::ANNULEE->value)->toBe('Annulée');
});

// Here's the dataset pattern: the function below runs ONCE PER ROW in ->with([]).
// Each row is an array; its items are passed as arguments to the closure in order.
// This is the same test logic run 4 times without copy-pasting.
it('returns the correct Filament label for each case', function (FormationStatus $status, string $expectedLabel) {
    expect($status->getLabel())->toBe($expectedLabel);
})->with([
    'planifiee' => [FormationStatus::PLANIFIEE, 'Planifiée'],
    'en cours'  => [FormationStatus::EN_COURS, 'En cours'],
    'terminee'  => [FormationStatus::TERMINEE, 'Terminée'],
    'annulee'   => [FormationStatus::ANNULEE, 'Annulée'],
]);

// tryFrom() is how you safely turn a raw string (e.g. coming from the DB or
// an API payload) back into an enum case. It returns null instead of throwing
// if the string doesn't match any case — this test locks in that safety net.
it('resolves a case from its raw value via tryFrom', function () {
    expect(FormationStatus::tryFrom('Terminée'))->toBe(FormationStatus::TERMINEE)
        ->and(FormationStatus::tryFrom('not-a-real-status'))->toBeNull();
});
