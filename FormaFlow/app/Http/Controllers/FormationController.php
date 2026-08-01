<?php

namespace App\Http\Controllers;

use App\Exceptions\DocumentGenerationException;
use App\Exceptions\SuppressionBloqueeException;
use App\Http\Requests\StoreFormationRequest;
use App\Http\Requests\UpdateFormationRequest;
use App\Models\Formation;
use App\Services\DocumentGenerationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormationController extends Controller
{
    /* GET /api/formations */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
            $formations = Formation::with('entrepriseCliente')->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'data'    => $formations->items(),
                'meta'    => [
                    'current_page' => $formations->currentPage(),
                    'last_page'    => $formations->lastPage(),
                    'per_page'     => $formations->perPage(),
                    'total'        => $formations->total(),
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur récupération liste Formation', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des formations.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* POST /api/formations */
    public function store(StoreFormationRequest $request): JsonResponse
    {
        try {
            $formation = DB::transaction(fn () =>
            Formation::create($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_CREATED,
                'message' => 'Formation créée avec succès.',
                'data'    => $formation->load('entrepriseCliente')
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erreur création Formation', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la formation.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* GET /api/formations/{formation} */
    public function show(Formation $formation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'data'    => $formation->load('entrepriseCliente')
        ], Response::HTTP_OK);
    }

    /* PUT/PATCH /api/formations/{formation} */
    public function update(UpdateFormationRequest $request, Formation $formation): JsonResponse
    {
        try {
            DB::transaction(fn () =>
            $formation->update($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Formation mise à jour avec succès.',
                'data'    => $formation->fresh()->load('entrepriseCliente')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour Formation', [
                'id'    => $formation->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la formation.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* GET /api/formations/{formation}/documents/modele-6?annee=2026 */
    public function genererModele6(
        Request $request,
        Formation $formation,
        DocumentGenerationService $documentGenerationService
    ): Response|JsonResponse {
        $validated = $request->validate([
            'annee' => ['required', 'integer', 'digits:4'],
        ]);

        try {
            $document = $documentGenerationService->generateModele6(
                $formation,
                (int) $validated['annee']
            );

            return response($document['content'], Response::HTTP_OK)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$document['filename'].'"');
        } catch (DocumentGenerationException $e) {
            return response()->json([
                'success' => false,
                'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            Log::error('Erreur génération Modèle 6', [
                'id'    => $formation->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération de l\'attestation.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* DELETE /api/formations/{formation} */


    public function destroy(Formation $formation): JsonResponse
    {
        try {
            DB::transaction(fn () =>
            $formation->delete()
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Formation supprimée avec succès.'
            ], Response::HTTP_OK);
        } catch (SuppressionBloqueeException $e) {
            return response()->json([
                'success' => false,
                'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            Log::error('Erreur suppression Formation', [
                'id'    => $formation->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la formation.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
