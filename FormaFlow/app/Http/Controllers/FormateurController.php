<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormateurRequest;
use App\Http\Requests\UpdateFormateurRequest;
use App\Models\Formateur;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormateurController extends Controller
{
    /* GET /api/formateurs */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
            $formateurs = Formateur::latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'data'    => $formateurs->items(),
                'meta'    => [
                    'current_page' => $formateurs->currentPage(),
                    'last_page'    => $formateurs->lastPage(),
                    'per_page'     => $formateurs->perPage(),
                    'total'        => $formateurs->total(),
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur récupération liste Formateur', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des formateurs.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* POST /api/formateurs */
    public function store(StoreFormateurRequest $request): JsonResponse
    {
        try {
            $formateur = DB::transaction(fn () =>
            Formateur::create($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_CREATED,
                'message' => 'Formateur créé avec succès.',
                'data'    => $formateur
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erreur création Formateur', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du formateur.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* GET /api/formateurs/{formateur} */
    public function show(Formateur $formateur): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'data'    => $formateur
        ], Response::HTTP_OK);
    }

    /* PUT/PATCH /api/formateurs/{formateur} */
    public function update(UpdateFormateurRequest $request, Formateur $formateur): JsonResponse
    {
        try {
            DB::transaction(fn () =>
            $formateur->update($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Formateur mis à jour avec succès.',
                'data'    => $formateur->fresh()
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour Formateur', [
                'id'    => $formateur->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du formateur.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* DELETE /api/formateurs/{formateur} */
    public function destroy(Formateur $formateur): JsonResponse
    {
        try {
            $formateur->delete();

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Formateur supprimé avec succès.'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur suppression Formateur', [
                'id'    => $formateur->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du formateur.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
