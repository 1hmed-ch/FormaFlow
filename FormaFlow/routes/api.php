<?php

use App\Http\Controllers\FormateurController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*Route::apiResource('/users', 'UserController');*/
Route::apiResource('/formateurs', FormateurController::class);
Route::apiResource('participants', ParticipantController::class);
