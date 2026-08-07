<?php

use App\Enums\CategorieDocumentGenere;
use App\Enums\StatutDocumentGenere;
use App\Models\DocumentGenere;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use App\Services\ArchiveZipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new ArchiveZipService();
    Storage::fake(config('documents.storage_disk', 'local'));
});

// IMPORTANT: unlike everything we've tested so far, ArchiveZipService writes
// the .zip itself via PHP's native ZipArchive straight to storage_path(),
// NOT through the Storage facade -- so Storage::fake() does NOT intercept it.
// That means these tests create a real file on disk. We clean it up after
// every test so your repo doesn't accumulate test .zip files.
afterEach(function () {
    \Illuminate\Support\Facades\File::deleteDirectory(storage_path('app/tmp'));
});

/**
 * Local helper: creates a DocumentGenere row AND writes matching fake
 * content to the (faked) disk, so existeSurLeDisque()/Storage::exists()
 * checks in the service see a real file, exactly like production.
 */
function creerDocumentGenereAvecFichier(EntrepriseCliente $entreprise, array $overrides = []): DocumentGenere
{
    $disk = config('documents.storage_disk', 'local');
    $chemin = 'documents/test/'.\Illuminate\Support\Str::random(10).'.pdf';

    if (! array_key_exists('chemin', $overrides)) {
        Storage::disk($disk)->put($chemin, '%PDF-1.4 fake content');
    }

    return DocumentGenere::create(array_merge([
        'documentable_type' => $entreprise->getMorphClass(),
        'documentable_id' => $entreprise->id,
        'type_document' => 'giac_g3_fiche_organisme_conseil',
        'categorie' => CategorieDocumentGenere::Giac,
        'nom_fichier' => 'document.pdf',
        'disque' => $disk,
        'chemin' => $chemin,
        'taille' => 100,
        'version' => 1,
        'statut' => StatutDocumentGenere::Genere,
        'genere_le' => now(),
    ], $overrides));
}

it('builds a zip containing every generated document, sorted into category folders', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    creerDocumentGenereAvecFichier($entreprise, [
        'categorie' => CategorieDocumentGenere::Giac,
        'nom_fichier' => 'g3.pdf',
    ]);
    creerDocumentGenereAvecFichier($entreprise, [
        'categorie' => CategorieDocumentGenere::Remboursement,
        'nom_fichier' => 'modele6.pdf',
    ]);

    $zipPath = $this->service->build($dossier);

    expect($zipPath)->toBeFile();

    $zip = new ZipArchive();
    $zip->open($zipPath);

    expect($zip->numFiles)->toBe(2)
        ->and($zip->locateName('Giac/g3.pdf'))->not->toBeFalse()
        ->and($zip->locateName('Remboursement/modele6.pdf'))->not->toBeFalse();

    $zip->close();
});

it('filters documents by categorie when one is given', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    creerDocumentGenereAvecFichier($entreprise, ['categorie' => CategorieDocumentGenere::Giac]);
    creerDocumentGenereAvecFichier($entreprise, ['categorie' => CategorieDocumentGenere::Remboursement]);

    $zipPath = $this->service->build($dossier, categorie: 'giac');

    $zip = new ZipArchive();
    $zip->open($zipPath);

    expect($zip->numFiles)->toBe(1);
    $zip->close();
});

it('filters documents by date range when both bounds are given', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    creerDocumentGenereAvecFichier($entreprise, ['genere_le' => '2026-01-10', 'nom_fichier' => 'old.pdf']);
    creerDocumentGenereAvecFichier($entreprise, ['genere_le' => '2026-06-10', 'nom_fichier' => 'recent.pdf']);

    $zipPath = $this->service->build($dossier, dateDebut: '2026-05-01', dateFin: '2026-07-01');

    $zip = new ZipArchive();
    $zip->open($zipPath);

    expect($zip->numFiles)->toBe(1)
        ->and($zip->locateName('Giac/recent.pdf'))->not->toBeFalse();
    $zip->close();
});

it('refuses to build a zip when the only matching document is missing from disk', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    // 'chemin' override with no matching Storage::put() call -- the row
    // exists, but the file behind it doesn't (e.g. manually deleted).
    creerDocumentGenereAvecFichier($entreprise, ['chemin' => 'documents/does-not-exist.pdf']);

    expect(fn () => $this->service->build($dossier))
        ->toThrow(RuntimeException::class);
});

it('refuses to build a zip when no documents exist at all', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    expect(fn () => $this->service->build($dossier))
        ->toThrow(RuntimeException::class);
});

it('does not add the same category+filename combo twice, keeping only the most recent', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    // Two rows sharing the same nom_fichier/categorie -- simulates
    // regenerating the same document type more than once.
    creerDocumentGenereAvecFichier($entreprise, [
        'nom_fichier' => 'g3.pdf',
        'genere_le' => '2026-01-01',
        'version' => 1,
    ]);
    creerDocumentGenereAvecFichier($entreprise, [
        'nom_fichier' => 'g3.pdf',
        'genere_le' => '2026-06-01',
        'version' => 2,
    ]);

    $zipPath = $this->service->build($dossier);

    $zip = new ZipArchive();
    $zip->open($zipPath);

    expect($zip->numFiles)->toBe(1);
    $zip->close();
});
