<?php

use App\Enums\CategorieDocumentGenere;
use App\Enums\StatutDocumentGenere;
use App\Models\DocumentGenere;
use App\Models\EntrepriseCliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/**
 * Local helper: a DocumentGenere row with a real (fake-disk) file behind it.
 */
function creerDocumentAvecFichierSurDisque(): DocumentGenere
{
    $disk = config('documents.storage_disk', 'local');
    $entreprise = EntrepriseCliente::factory()->create();
    $chemin = 'documents/telechargement-test.pdf';
    Storage::disk($disk)->put($chemin, '%PDF-1.4 fake content');

    return DocumentGenere::create([
        'documentable_type' => $entreprise->getMorphClass(),
        'documentable_id' => $entreprise->id,
        'type_document' => 'modele6',
        'categorie' => CategorieDocumentGenere::Remboursement,
        'nom_fichier' => 'modele6.pdf',
        'disque' => $disk,
        'chemin' => $chemin,
        'taille' => 100,
        'version' => 1,
        'statut' => StatutDocumentGenere::Genere,
        'genere_le' => now(),
    ]);
}

beforeEach(function () {
    Storage::fake(config('documents.storage_disk', 'local'));
});

// --- telecharger (download) -- protected by auth ----------------------------

it('redirects a guest away from the download route instead of serving the file', function () {
    $document = creerDocumentAvecFichierSurDisque();

    $this->get(route('documents-generes.telecharger', $document))
        ->assertRedirect(route('filament.admin.auth.login')); // Laravel's default 'auth' middleware redirect-to-login
});

it('lets an authenticated user download a document that exists on disk', function () {
    $document = creerDocumentAvecFichierSurDisque();

    $response = $this->actingAs(User::factory()->create())
        ->get(route('documents-generes.telecharger', $document));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('modele6.pdf');
});

it('returns a 404 when the archive row exists but the file is gone from disk', function () {
    $document = creerDocumentAvecFichierSurDisque();
    Storage::disk($document->disque)->delete($document->chemin);

    $this->actingAs(User::factory()->create())
        ->get(route('documents-generes.telecharger', $document))
        ->assertNotFound();
});

// --- stream -- this route was unprotected until the routes/web.php fix above ---

it('redirects a guest away from the stream route (regression test for the missing auth middleware)', function () {
    $document = creerDocumentAvecFichierSurDisque();

    $this->get(route('documents-generes.stream', $document))
        ->assertRedirect(route('filament.admin.auth.login'));
});

it('lets an authenticated user stream a document inline as a PDF', function () {
    $document = creerDocumentAvecFichierSurDisque();

    $response = $this->actingAs(User::factory()->create())
        ->get(route('documents-generes.stream', $document));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});
