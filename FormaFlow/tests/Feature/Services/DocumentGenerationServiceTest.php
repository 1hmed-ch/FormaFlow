<?php

use App\Enums\TypeFormation;
use App\Exceptions\DocumentGenerationException;
use App\Models\DocumentGenere;
use App\Models\DossierGiac;
use App\Models\EntrepriseCliente;
use App\Models\Formation;
use App\Models\Gerant;
use App\Models\Groupe;
use App\Models\Participant;
use App\Models\Theme;
use App\Services\DocumentGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new DocumentGenerationService();
    Storage::fake(config('documents.storage_disk', 'local'));
});

/**
 * Small local helper: an EntrepriseCliente with a gerant attached, since
 * more than half of these methods guard on "gerant renseigné" first.
 * Keeps the tests below focused on the ONE rule each is checking.
 */
function entrepriseAvecGerant(array $overrides = []): EntrepriseCliente
{
    $gerant = Gerant::factory()->create();

    return EntrepriseCliente::factory()->create(array_merge(
        ['gerant_id' => $gerant->id],
        $overrides
    ));
}

// --- generateModele6 -------------------------------------------------------

it('refuses Modele 6 when the formation is not Terminee', function () {
    $formation = Formation::factory()->create(); // default statut: Planifiée

    expect(fn () => $this->service->generateModele6($formation, 2026))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses Modele 6 when the entreprise has no gerant', function () {
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => null]);
    $formation = Formation::factory()->terminee()->create(['entreprise_id' => $entreprise->id]);

    expect(fn () => $this->service->generateModele6($formation, 2026))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses Modele 6 when the formation has no themes', function () {
    $entreprise = entrepriseAvecGerant();
    $formation = Formation::factory()->terminee()->create(['entreprise_id' => $entreprise->id]);

    expect(fn () => $this->service->generateModele6($formation, 2026))
        ->toThrow(DocumentGenerationException::class);
});

it('generates Modele 6 and archives it under remboursement with the formation_id in metadata', function () {
    $entreprise = entrepriseAvecGerant();
    $formation = Formation::factory()->terminee()->create(['entreprise_id' => $entreprise->id]);
    Theme::factory()->create(['formation_id' => $formation->id]);

    $result = $this->service->generateModele6($formation, 2026);

    expect($result['content'])->toStartWith('%PDF');

    $this->assertDatabaseHas('documents_generes', [
        'type_document' => 'modele6',
        'categorie' => 'remboursement',
    ]);

    expect(DocumentGenere::first()->metadonnees)->toBe([
        'annee' => 2026,
        'formation_id' => $formation->id,
    ]);
});

// --- generateFichePresence -------------------------------------------------

it('refuses the fiche de presence when the groupe has no participants', function () {
    $groupe = Groupe::factory()->create();

    expect(fn () => $this->service->generateFichePresence($groupe))
        ->toThrow(DocumentGenerationException::class);
});

it('generates the fiche de presence and archives it under remboursement', function () {
    $groupe = Groupe::factory()->create();
    attacherParticipant($groupe);

    $result = $this->service->generateFichePresence($groupe);

    expect($result['content'])->toStartWith('%PDF');
    $this->assertDatabaseHas('documents_generes', [
        'type_document' => 'modele5_fiche_presence',
        'categorie' => 'remboursement',
    ]);
});

// --- generateFicheEvaluation ------------------------------------------------

it('refuses the fiche evaluation when the groupe has no participants', function () {
    $groupe = Groupe::factory()->create();

    expect(fn () => $this->service->generateFicheEvaluation($groupe, 'Rabat'))
        ->toThrow(DocumentGenerationException::class);
});

it('generates the fiche evaluation with ville recorded in metadata', function () {
    $groupe = Groupe::factory()->create();
    attacherParticipant($groupe);

    $this->service->generateFicheEvaluation($groupe, 'Rabat');

    expect(DocumentGenere::where('type_document', 'fiche_evaluation_synthetique')->first()->metadonnees)
        ->toMatchArray(['ville' => 'Rabat']);
});

// --- generateB1BulletinAdhesion --------------------------------------------

it('refuses the G1 bulletin adhesion when the entreprise has no gerant', function () {
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => null]);

    expect(fn () => $this->service->generateB1BulletinAdhesion($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

it('generates G1 even with no formations yet, defaulting the annee to today', function () {
    $entreprise = entrepriseAvecGerant();

    $this->service->generateB1BulletinAdhesion($entreprise);

    expect(DocumentGenere::where('type_document', 'giac_g1_bulletin_adhesion')->first()->metadonnees)
        ->toMatchArray(['annee' => now()->year, 'groupe_id' => null]);
});

// --- generateCFicheEntreprise (G2) ------------------------------------------

it('refuses G2 when the entreprise has no gerant', function () {
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => null]);

    expect(fn () => $this->service->generateCFicheEntreprise($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses G2 when effectif_total is zero or not set', function () {
    $entreprise = entrepriseAvecGerant(['effectif_total' => 0]);

    expect(fn () => $this->service->generateCFicheEntreprise($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

it('generates G2 when gerant and effectif_total are both present', function () {
    $entreprise = entrepriseAvecGerant(['effectif_total' => 25]);

    $result = $this->service->generateCFicheEntreprise($entreprise);

    expect($result['content'])->toStartWith('%PDF');
    $this->assertDatabaseHas('documents_generes', ['type_document' => 'giac_g2_fiche_entreprise']);
});

// --- generateGDeclarationHonneur (G5) ---------------------------------------
// This one also exercises the protected determinerTypeFormation() helper
// indirectly -- there's no public way to call it directly, so testing it
// through the one method that uses it is the right level here.

it('refuses G5 when the entreprise has no gerant', function () {
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => null, 'ville' => 'Fes']);

    expect(fn () => $this->service->generateGDeclarationHonneur($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses G5 when the entreprise has no ville', function () {
    $entreprise = entrepriseAvecGerant(['ville' => null]);

    expect(fn () => $this->service->generateGDeclarationHonneur($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses G5 when the entreprise has no formations at all', function () {
    $entreprise = entrepriseAvecGerant(['ville' => 'Fes']);

    expect(fn () => $this->service->generateGDeclarationHonneur($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

it('generates G5 and resolves the type_formation from the formation themes', function () {
    $entreprise = entrepriseAvecGerant(['ville' => 'Fes']);
    $formation = Formation::factory()->create([
        'entreprise_id' => $entreprise->id,
        'type_formation' => TypeFormation::INGENIERIE,
    ]);
    Theme::factory()->create([
        'formation_id' => $formation->id,
        'date_fin' => '2026-05-15',
    ]);

    $this->service->generateGDeclarationHonneur($entreprise);

    expect(DocumentGenere::where('type_document', 'giac_g5_declaration_honneur')->first()->metadonnees)
        ->toBe(['annee' => 2026, 'type_formation' => 'ingenierie']);
});

// --- generateFicheAccesClient ------------------------------------------------

it('refuses the fiche acces client when the entreprise has no gerant', function () {
    $entreprise = EntrepriseCliente::factory()->create(['gerant_id' => null]);

    expect(fn () => $this->service->generateFicheAccesClient($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

// --- generateDFicheTechniqueDiagnostic / generateEFicheTechniqueIngenierie --

it('refuses fiche D when the entreprise has no etude de diagnostic strategique', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    expect(fn () => $this->service->generateDFicheTechniqueDiagnostic($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

it('refuses fiche E when the entreprise has no etude d\'ingenierie de formation', function () {
    $entreprise = EntrepriseCliente::factory()->create();

    expect(fn () => $this->service->generateEFicheTechniqueIngenierie($entreprise))
        ->toThrow(DocumentGenerationException::class);
});

// --- generateDossierGiac (orchestrator) -------------------------------------

it('generates all seven GIAC documents and creates the master DossierGiac record', function () {
    $entreprise = entrepriseAvecGerant(['ville' => 'Fes', 'effectif_total' => 25]);
    $formation = Formation::factory()->create(['entreprise_id' => $entreprise->id]);
    Theme::factory()->create(['formation_id' => $formation->id, 'date_fin' => '2026-05-15']);

    // D and E each need a real etude to render since they were fixed to
    // require one -- without these, the orchestrator would (correctly) throw.
    \App\Models\EtudeDiagnosticStrategique::create(['entreprise_id' => $entreprise->id]);
    \App\Models\EtudeIngenierieFormation::create(['entreprise_id' => $entreprise->id]);

    $documents = $this->service->generateDossierGiac($entreprise);

    expect($documents)->toHaveKeys(['B1', 'B2', 'C', 'D', 'E', 'F', 'G']);

    $expectedTypes = [
        'giac_g1_bulletin_adhesion',
        'giac_g7_bulletin_readhesion',
        'giac_g2_fiche_entreprise',
        'giac_g6_fiche_diagnostic_strategique',
        'giac_g4_fiche_ingenierie_formation',
        'giac_g3_fiche_organisme_conseil',
        'giac_g5_declaration_honneur',
    ];
    foreach ($expectedTypes as $type) {
        $this->assertDatabaseHas('documents_generes', ['type_document' => $type]);
    }

    $this->assertDatabaseHas('dossiers_giac', [
        'entreprise_cliente_id' => $entreprise->id,
        'statut' => \App\Enums\StatutDossierGiac::EnCours->value,
    ]);
});

/**
 * Local helper: builds a Participant and attaches it to a Groupe through
 * the groupe_participant pivot. Used by both fiche-presence and
 * fiche-evaluation tests above.
 */
function attacherParticipant(Groupe $groupe): Participant
{
    $participant = Participant::create([
        'nom' => 'Test',
        'prenom' => 'Participant',
        'cin' => 'CD'.fake()->unique()->numerify('######'),
        'categorie_sp' => 'E',
        'entreprise_id' => EntrepriseCliente::factory()->create()->id,
    ]);

    $groupe->participants()->attach($participant->id);

    return $participant;
}
