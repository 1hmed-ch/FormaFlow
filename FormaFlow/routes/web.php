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
    ->name('documents-generes.stream');
