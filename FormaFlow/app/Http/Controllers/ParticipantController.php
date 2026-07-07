<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use App\model\Participant;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Participant::with('entreprise')->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParticipantRequest $request)
    {
        $participant = Participant::create($request->validated());
        return response()->json($participant, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Participant $participant)
    {
        return response()->json($participant->load('entreprise'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParticipantRequest $request, Participant $participant)
    {
        $participant->update($request->validated());
        return response()->json($participant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participant $participant)
    {
        $participant->delete();
        return response()->json(['message' => 'Participant supprimé avec succès']);
    }
}
