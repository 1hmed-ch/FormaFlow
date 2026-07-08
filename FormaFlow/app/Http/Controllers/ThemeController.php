<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreThemeRequest;
use App\Http\Requests\UpdateThemeRequest;
use App\Models\Theme;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThemeController extends Controller
{
    /* GET /api/themes */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
            
            $themes = Theme::with(['formation', 'formateur'])->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'data'    => $themes->items(),
                'meta'    => [
                    'current_page' => $themes->currentPage(),
                    'last_page'    => $themes->lastPage(),
                    'per_page'     => $themes->perPage(),
                    'total'        => $themes->total(),
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur récupération liste Thème', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des thèmes.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* POST /api/themes */
    public function store(StoreThemeRequest $request): JsonResponse
    {
        try {
            $theme = DB::transaction(fn () =>
                Theme::create($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_CREATED,
                'message' => 'Thème créé avec succès.',
                'data'    => $theme
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erreur création Thème', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du thème.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* GET /api/themes/{theme} */
    public function show(Theme $theme): JsonResponse
    {
        $theme->load(['formation', 'formateur']);

        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'data'    => $theme
        ], Response::HTTP_OK);
    }

    /* PUT/PATCH /api/themes/{theme} */
    public function update(UpdateThemeRequest $request, Theme $theme): JsonResponse
    {
        try {
            DB::transaction(fn () =>
                $theme->update($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Thème mis à jour avec succès.',
                'data'    => $theme->fresh(['formation', 'formateur'])
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour Thème', [
                'id'    => $theme->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du thème.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* DELETE /api/themes/{theme} */
    public function destroy(Theme $theme): JsonResponse
    {
        try {
            $theme->delete();

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Thème supprimé avec succès.'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur suppression Thème', [
                'id'    => $theme->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du thème.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}