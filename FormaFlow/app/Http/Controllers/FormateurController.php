<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormateurRequest;
use App\Http\Requests\UpdateFormateurRequest;
use App\model\Formateur;

class FormateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Formateur::latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormateurRequest $request)
    {
        $formateur = Formateur::create($request->validated());
        return response()->json($formateur, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Formateur $formateur)
    {
        return response()->json($formateur);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormateurRequest $request, Formateur $formateur)
    {
        $formateur->update($request->validated());
        return response()->json($formateur);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formateur $formateur)
    {
        $formateur->delete();
        return response()->json(['message' => 'Formateur supprimé avec succès']);
    }
}
