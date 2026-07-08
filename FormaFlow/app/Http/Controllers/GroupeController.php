<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupeRequest;
use App\Http\Requests\UpdateGroupeRequest;
use App\Models\Groupe;
use App\Models\Participant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupeController extends Controller
{
    /* GET /api/groupes */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
            $groupes = Groupe::with(['theme.formation', 'participants'])
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'data'    => $groupes->items(),
                'meta'    => [
                    'current_page' => $groupes->currentPage(),
                    'last_page'    => $groupes->lastPage(),
                    'per_page'     => $groupes->perPage(),
                    'total'        => $groupes->total(),
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur récupération liste Groupe', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des groupes.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* POST /api/groupes */
    public function store(StoreGroupeRequest $request): JsonResponse
    {
        try {
            $groupe = DB::transaction(fn () =>
            Groupe::create($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_CREATED,
                'message' => 'Groupe créé avec succès.',
                'data'    => $groupe->load('theme'),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erreur création Groupe', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du groupe.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* GET /api/groupes/{groupe} */
    public function show(Groupe $groupe): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'data'    => $groupe->load(['theme.formation', 'participants']),
        ], Response::HTTP_OK);
    }

    /* PUT/PATCH /api/groupes/{groupe} */
    public function update(UpdateGroupeRequest $request, Groupe $groupe): JsonResponse
    {
        try {
            DB::transaction(fn () =>
            $groupe->update($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Groupe mis à jour avec succès.',
                'data'    => $groupe->fresh(['theme', 'participants']),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour Groupe', [
                'id'    => $groupe->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du groupe.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* DELETE /api/groupes/{groupe} */
    public function destroy(Groupe $groupe): JsonResponse
    {
        try {
            $groupe->delete();

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Groupe supprimé avec succès.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur suppression Groupe', [
                'id'    => $groupe->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du groupe.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/groupes/{groupe}/participants
     * Body: { "participant_ids": [1, 2, 3] }
     *
     * Enforces three rules the ticket's acceptance criteria don't mention
     * but the CSF requires:
     *  1. effectif_max is a hard cap.
     *  2. Participant must belong to the same entreprise as the Formation.
     *  3. A participant can only be in ONE group per Theme.
     */
    public function attachParticipants(Request $request, Groupe $groupe): JsonResponse
    {
        $validated = $request->validate([
            'participant_ids'   => 'required|array|min:1',
            'participant_ids.*' => 'integer|exists:participants,id',
        ]);

        $groupe->loadMissing('theme.formation');
        $entrepriseId = $groupe->theme->formation->entreprise_id;

        $rejected = [];
        $toAttach = [];

        $currentCount = $groupe->participants()->count();
        $availableSlots = $groupe->effectif_max - $currentCount;

        foreach ($validated['participant_ids'] as $participantId) {
            $participant = Participant::find($participantId);

            if ($participant->entreprise_id !== $entrepriseId) {
                $rejected[] = [
                    'participant_id' => $participantId,
                    'reason' => "N'appartient pas à l'entreprise cliente de cette formation.",
                ];
                continue;
            }

            $alreadyInTheme = Participant::where('id', $participantId)
                ->whereHas('groupes', function ($q) use ($groupe) {
                    $q->where('groupes.theme_id', $groupe->theme_id)
                        ->where('groupes.id', '!=', $groupe->id);
                })->exists();

            if ($alreadyInTheme) {
                $rejected[] = [
                    'participant_id' => $participantId,
                    'reason' => 'Déjà affecté à un autre groupe de ce thème.',
                ];
                continue;
            }

            $toAttach[] = $participantId;
        }

        if (count($toAttach) > $availableSlots) {
            return response()->json([
                'success' => false,
                'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "Capacité du groupe dépassée : {$availableSlots} place(s) disponible(s), "
                    . count($toAttach) . ' demandée(s).',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $groupe->participants()->syncWithoutDetaching($toAttach);

        return response()->json([
            'success'  => true,
            'status'   => Response::HTTP_OK,
            'message'  => empty($rejected)
                ? 'Participants affectés avec succès.'
                : 'Affectation partielle : certains participants ont été rejetés.',
            'attached' => $toAttach,
            'rejected' => $rejected,
            'data'     => $groupe->fresh('participants'),
        ], Response::HTTP_OK);
    }

    /* DELETE /api/groupes/{groupe}/participants/{participant} */
    public function detachParticipant(Groupe $groupe, Participant $participant): JsonResponse
    {
        $groupe->participants()->detach($participant->id);

        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'message' => 'Participant retiré du groupe.',
            'data'    => $groupe->fresh('participants'),
        ], Response::HTTP_OK);
    }
}
