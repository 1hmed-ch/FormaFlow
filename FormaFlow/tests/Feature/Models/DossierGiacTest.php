<?php

use App\Enums\StatutDossierGiac;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('casts statut to the StatutDossierGiac enum', function () {
    $dossier = DossierGiac::factory()->create();

    expect($dossier->statut)->toBe(StatutDossierGiac::EnCours);
});

it('belongs to an entreprise cliente', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    expect($dossier->entrepriseCliente->id)->toBe($entreprise->id);
});

it('reports 0% progression when no pieces jointes have been uploaded', function () {
    $dossier = DossierGiac::factory()->create();

    expect($dossier->getProgressionArchive())->toBe(0);
});

it('flips statut to Signe once all pieces jointes are uploaded', function () {
    // Storage::fake() swaps the real disk for an in-memory fake for this
    // test only — nothing is ever written to your actual storage folder.
    // NOTE: if your media-library config uses a disk other than 'public',
    // change this to match (check config/media-library.php -> disk_name).
    Storage::fake('public');

    $dossier = DossierGiac::factory()->create();

    // DossierGiac::PIECES_JOINTES holds all 9 required document slots —
    // "upload" a fake PDF into every single one.
    // "upload" a fake PDF into every single one. The %PDF- header is enough
    // for real mime-sniffing (finfo) to recognize it as application/pdf —
    // Spatie MediaLibrary validates real file bytes, not just a claimed mime type.
    foreach (array_keys(DossierGiac::PIECES_JOINTES) as $collection) {
        $dossier->addMediaFromString('%PDF-1.4 fake content for testing')
            ->usingFileName("{$collection}.pdf")
            ->toMediaCollection($collection);
    }

    $progression = $dossier->getProgressionArchive();

    expect($progression)->toBe(100)
        ->and($dossier->fresh()->statut)->toBe(StatutDossierGiac::Signe);
});

it('pourEntreprise returns the existing dossier instead of creating a duplicate', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $first = DossierGiac::pourEntreprise($entreprise);
    $second = DossierGiac::pourEntreprise($entreprise);

    expect($second->id)->toBe($first->id)
        ->and(DossierGiac::where('entreprise_cliente_id', $entreprise->id)->count())->toBe(1);
});
