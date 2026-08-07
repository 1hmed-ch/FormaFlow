<?php

use App\Http\Controllers\DocumentGenereDownloadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaStreamController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/documents-generes/{documentGenere}/telecharger', DocumentGenereDownloadController::class)
    ->middleware('auth')
    ->name('documents-generes.telecharger');
Route::middleware(['auth'])
    ->get('/admin/media/{media}/stream', [MediaStreamController::class, 'stream'])
    ->name('media.stream');
Route::get('/documents-generes/{documentGenere}/stream', [DocumentGenereDownloadController::class, 'stream'])
    ->middleware('auth')
    ->name('documents-generes.stream');
Route::get('/entreprises-clientes/{record}/stream-entete', function ($record) {
    $entreprise = \App\Models\EntrepriseCliente::findOrFail($record);
    $content = base64_decode($entreprise->getEnteteImageBase64());
    return response($content, 200, ['Content-Type' => 'image/png']);
})->middleware('auth')->name('entreprise.stream-entete');

Route::get('/entreprises-clientes/{record}/stream-pied-page', function ($record) {
    $entreprise = \App\Models\EntrepriseCliente::findOrFail($record);
    $content = base64_decode($entreprise->getPiedPageImageBase64());
    return response($content, 200, ['Content-Type' => 'image/png']);
})->middleware('auth')->name('entreprise.stream-pied-page');