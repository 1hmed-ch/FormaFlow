<?php

use App\Http\Controllers\EntrepriseClienteController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ThemeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupeController;

// Routing pour la gestion des entreprises clientes

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*Route::apiResource('/users', 'UserController');*/
Route::apiResource('/entreprise-clientes', EntrepriseClienteController::class);
Route::apiResource('/formateurs', FormateurController::class);
Route::apiResource('/formations', FormationController::class);
Route::apiResource('/participants', ParticipantController::class);
Route::apiResource('themes', ThemeController::class);

Route::apiResource('/groupes', GroupeController::class);
Route::post('/groupes/{groupe}/participants', [GroupeController::class, 'attachParticipants']);
Route::delete('/groupes/{groupe}/participants/{participant}', [GroupeController::class, 'detachParticipant']);
