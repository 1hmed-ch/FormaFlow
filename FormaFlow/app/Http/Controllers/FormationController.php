<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormationRequest;
use App\Http\Requests\UpdateFormationRequest;
use App\Models\Formation;

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Formation::with('entrepriseCliente')->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormationRequest $request)
    {
        $formation = Formation::create($request->validated());
        return response()->json($formation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Formation $formation)
    {
        return response()->json($formation->load('entrepriseCliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormationRequest $request, Formation $formation)
    {
        $formation->update($request->validated());
        return response()->json($formation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation)
    {
        $formation->delete();
        return response()->json(['message' => 'Formation supprimée avec succès']);
    }
}