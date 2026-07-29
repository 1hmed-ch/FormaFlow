<?php

use App\Http\Controllers\DocumentGenereDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documents-generes/{documentGenere}/telecharger', DocumentGenereDownloadController::class)
    ->middleware('auth')
    ->name('documents-generes.telecharger');
