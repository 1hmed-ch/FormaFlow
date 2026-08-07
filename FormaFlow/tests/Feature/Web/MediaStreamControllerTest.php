<?php

use App\Models\EntrepriseCliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // EntrepriseCliente's media collections don't specify ->useDisk(), so
    // Spatie MediaLibrary falls back to its own default disk ('public'
    // unless MEDIA_DISK is set) -- fake that one specifically.
    Storage::fake('public');
});

/**
 * Local helper: attaches a real (fake-disk) file to an entreprise's
 * 'autres_documents' collection, the way an admin would via the Filament panel.
 */
function creerMediaSurEntreprise(): \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    $entreprise = EntrepriseCliente::factory()->create();

    return $entreprise->addMediaFromString('%PDF-1.4 fake content')
        ->usingFileName('piece-jointe.pdf')
        ->toMediaCollection('autres_documents');
}

it('redirects a guest away from the media stream route', function () {
    $media = creerMediaSurEntreprise();

    $this->get(route('media.stream', $media))->assertRedirect(route('filament.admin.auth.login'));
});

it('lets an authenticated user stream the media file inline', function () {
    $media = creerMediaSurEntreprise();

    $response = $this->actingAs(User::factory()->create())
        ->get(route('media.stream', $media));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('piece-jointe.pdf');
});

it('returns a 404 when the media row exists but the underlying file is gone', function () {
    $media = creerMediaSurEntreprise();
    @unlink($media->getPath());

    $this->actingAs(User::factory()->create())
        ->get(route('media.stream', $media))
        ->assertNotFound();
});
