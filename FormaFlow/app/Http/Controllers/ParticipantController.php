<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use App\Models\Participant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParticipantController extends Controller
{
    /* GET /api/participants */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
            $participants = Participant::with('entrepriseCliente')->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'data'    => $participants->items(),
                'meta'    => [
                    'current_page' => $participants->currentPage(),
                    'last_page'    => $participants->lastPage(),
                    'per_page'     => $participants->perPage(),
                    'total'        => $participants->total(),
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur récupération liste Participant', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des participants.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* POST /api/participants */
    public function store(StoreParticipantRequest $request): JsonResponse
    {
        try {
            $participant = DB::transaction(fn () =>
            Participant::create($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_CREATED,
                'message' => 'Participant créé avec succès.',
                'data'    => $participant->load('entrepriseCliente')
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erreur création Participant', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du participant.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* GET /api/participants/{participant} */
    public function show(Participant $participant): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'data'    => $participant->load('entrepriseCliente')
        ], Response::HTTP_OK);
    }

    /* PUT/PATCH /api/participants/{participant} */
    public function update(UpdateParticipantRequest $request, Participant $participant): JsonResponse
    {
        try {
            DB::transaction(fn () =>
            $participant->update($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Participant mis à jour avec succès.',
                'data'    => $participant->fresh('entrepriseCliente')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour Participant', [
                'id'    => $participant->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du participant.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* DELETE /api/participants/{participant} */
    public function destroy(Participant $participant): JsonResponse
    {
        try {
            $participant->delete();

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Participant supprimé avec succès.'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur suppression Participant', [
                'id'    => $participant->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du participant.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
