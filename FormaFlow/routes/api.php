<?php

use App\Http\Controllers\EntrepriseClienteController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\FormateurController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routing pour la gestion des entreprises clientes

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*Route::apiResource('/users', 'UserController');*/
Route::apiResource('entreprise-clientes', EntrepriseClienteController::class);
Route::apiResource('/formateurs', FormateurController::class);
Route::apiResource('participants', ParticipantController::class);
