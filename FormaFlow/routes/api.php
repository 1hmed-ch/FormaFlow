<?php

use App\Http\Controllers\EntrepriseClienteController;
use Illuminate\Support\Facades\Route;

// Routing pour la gestion des entreprises clientes
Route::apiResource('entreprise-clientes', EntrepriseClienteController::class);