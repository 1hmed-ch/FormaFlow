<?php

use App\Exceptions\DocumentGenerationException;
use App\Models\DocumentGenere;
use App\Models\EntrepriseCliente;
use App\Models\EtudeDiagnosticStrategique;
use App\Models\EtudeIngenierieFormation;
use App\Models\Gerant;
use App\Services\GiacDocumentGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new GiacDocumentGenerationService();
    // Every generation method writes the PDF to disk before creating its
    // archive row -- fake the configured disk so nothing touches real storage.
    Storage::fake(config('documents.storage_disk', 'local'));
});

// --- Guard clauses -------------------------------------------------------
// These don't touch Dompdf/Blade at all: the exception is thrown before
// rendering ever happens, so these tests are fast and have no rendering
// dependencies to worry about.

it('refuses to generate G4 when the etude belongs to a different entreprise', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $autreEntreprise = EntrepriseCliente::factory()->create();
    $etude = EtudeIngenierieFormation::create(['entreprise_id' => $autreEntreprise->id]);

    expect(fn () => $this->service->generateFicheIngenierieFormation($entreprise, $etude))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses to generate G6 when the etude belongs to a different entreprise', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $autreEntreprise = EntrepriseCliente::factory()->create();
    $etude = EtudeDiagnosticStrategique::create(['entreprise_id' => $autreEntreprise->id]);

    expect(fn () => $this->service->generateFicheDiagnosticStrategique($entreprise, $etude))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses to generate the readhesion bulletin when the entreprise has no gerant', function () {
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => null]);

    expect(fn () => $this->service->generateBulletinReadhesion($entreprise, 2026))
        ->toThrow(DocumentGenerationException::class);
});

// --- Happy paths -----------------------------------------------------------
// These actually render the real Blade templates through Dompdf, so they
// double as a check that your .blade.php files don't error out on real data.

it('generates G3 and archives it against the entreprise', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    $result = $this->service->generateFicheOrganismeConseil($entreprise);

    expect($result['filename'])->toBe('giac_g3_fiche_organisme_conseil.pdf')
        // %PDF is the file-format magic header -- this proves Dompdf actually
        // produced a real PDF, not just some HTML string.
        ->and($result['content'])->toStartWith('%PDF');

    $this->assertDatabaseHas('documents_generes', [
        'documentable_type' => $entreprise->getMorphClass(),
        'documentable_id' => $entreprise->id,
        'type_document' => 'giac_g3_fiche_organisme_conseil',
        'categorie' => 'giac',
        'version' => 1,
    ]);

    $document = DocumentGenere::first();
    Storage::disk(config('documents.storage_disk', 'local'))->assertExists($document->chemin);
});

it('generates G7 with the correct annee metadata when the entreprise has a gerant', function () {
    $gerant = Gerant::factory()->create();
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => $gerant->id]);

    $result = $this->service->generateBulletinReadhesion($entreprise, 2026);

    expect($result['content'])->toStartWith('%PDF');

    $this->assertDatabaseHas('documents_generes', [
        'type_document' => 'giac_g7_bulletin_readhesion',
        'categorie' => 'giac',
    ]);

    expect(DocumentGenere::first()->metadonnees)->toBe(['annee' => 2026]);
});

it('generates the F3 OFPPT form under the ofppt categorie, not giac', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    $this->service->generateF3FicheIdentificationOrganisme($entreprise);

    $this->assertDatabaseHas('documents_generes', [
        'type_document' => 'f3_fiche_identification_organisme',
        'categorie' => 'ofppt',
    ]);
});

it('increments the version number on every regeneration instead of overwriting', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    $this->service->generateFicheOrganismeConseil($entreprise);
    $this->service->generateFicheOrganismeConseil($entreprise);

    expect(DocumentGenere::where('type_document', 'giac_g3_fiche_organisme_conseil')->count())->toBe(2);

    $versions = DocumentGenere::where('type_document', 'giac_g3_fiche_organisme_conseil')
        ->orderBy('version')
        ->pluck('version')
        ->all();

    expect($versions)->toBe([1, 2]);
});
