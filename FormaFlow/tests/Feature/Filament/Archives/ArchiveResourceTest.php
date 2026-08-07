<?php

use App\Enums\CategorieDocumentGenere;
use App\Enums\StatutDocumentGenere;
use App\Filament\Resources\Archives\Pages\ListArchives;
use App\Filament\Resources\Archives\Pages\ViewArchive;
use App\Models\DocumentGenere;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake(config('documents.storage_disk', 'local'));
});

//  helper local pour créer un "document généré" rattaché à une entreprise
function creerDocumentGenerePourArchive(EntrepriseCliente $entreprise, array $overrides = []): DocumentGenere
{
    return DocumentGenere::create(array_merge([
        'documentable_type' => $entreprise->getMorphClass(),
        'documentable_id' => $entreprise->id,
        'type_document' => 'giac_g3_fiche_organisme_conseil',
        'categorie' => CategorieDocumentGenere::Giac,
        'nom_fichier' => 'document.pdf',
        'disque' => config('documents.storage_disk', 'local'),
        'chemin' => 'documents/test/'.\Illuminate\Support\Str::random(10).'.pdf',
        'taille' => 100,
        'version' => 1,
        'statut' => StatutDocumentGenere::Genere,
        'genere_le' => now(),
    ], $overrides));
}

// 1. LISTE

it('affiche la page liste des archives sans erreur', function () {
    Livewire::test(ListArchives::class)->assertSuccessful();
});

it('affiche les dossiers archivés existants dans la table', function () {
    $dossiers = DossierGiac::factory()->count(3)->create();

    Livewire::test(ListArchives::class)
        ->assertCanSeeTableRecords($dossiers);
});

// 2. VUE DÉTAILLÉE

it("affiche la page de détail d'une archive sans erreur", function () {
    $dossier = DossierGiac::factory()->create();

    Livewire::test(ViewArchive::class, ['record' => $dossier->id])
        ->assertSuccessful();
});

// 3. FILTRE 
it("affiche tous les documents générés quand aucun filtre n'est actif", function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    creerDocumentGenerePourArchive($entreprise, ['nom_fichier' => 'giac-report.pdf', 'categorie' => CategorieDocumentGenere::Giac]);
    creerDocumentGenerePourArchive($entreprise, ['nom_fichier' => 'ofppt-report.pdf', 'categorie' => CategorieDocumentGenere::Ofppt]);

    Livewire::test(ViewArchive::class, ['record' => $dossier->id])
        ->assertSee('giac-report.pdf')
        ->assertSee('ofppt-report.pdf');
});

it('filtre les documents générés par catégorie', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    creerDocumentGenerePourArchive($entreprise, ['nom_fichier' => 'giac-report.pdf', 'categorie' => CategorieDocumentGenere::Giac]);
    creerDocumentGenerePourArchive($entreprise, ['nom_fichier' => 'ofppt-report.pdf', 'categorie' => CategorieDocumentGenere::Ofppt]);

    Livewire::test(ViewArchive::class, ['record' => $dossier->id])
        ->set('archiveDocumentsCategorie', CategorieDocumentGenere::Giac->value)
        ->assertSee('giac-report.pdf')
        ->assertDontSee('ofppt-report.pdf');
});

it('filtre les documents générés par intervalle de dates', function () {
    $entreprise = EntrepriseCliente::factory()->create();
    $dossier = DossierGiac::factory()->create(['entreprise_cliente_id' => $entreprise->id]);

    creerDocumentGenerePourArchive($entreprise, ['nom_fichier' => 'ancien.pdf', 'genere_le' => now()->subMonths(3)]);
    creerDocumentGenerePourArchive($entreprise, ['nom_fichier' => 'recent.pdf', 'genere_le' => now()]);

    Livewire::test(ViewArchive::class, ['record' => $dossier->id])
        ->set('archiveDocumentsDateDebut', now()->subDay()->toDateString())
        ->assertSee('recent.pdf')
        ->assertDontSee('ancien.pdf');
});