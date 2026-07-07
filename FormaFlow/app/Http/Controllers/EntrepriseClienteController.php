<?php

namespace App\Http\Controllers;

use App\Models\EntrepriseCliente;
use App\Http\Requests\StoreEntrepriseClienteRequest;
use App\Http\Requests\UpdateEntrepriseClienteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EntrepriseClienteController extends Controller
{
    /* GET /api/entreprise-clientes */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
            $entreprises = EntrepriseCliente::paginate($perPage);

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'data'    => $entreprises->items(),
                'meta'    => [
                    'current_page' => $entreprises->currentPage(),
                    'last_page'    => $entreprises->lastPage(),
                    'per_page'     => $entreprises->perPage(),
                    'total'        => $entreprises->total(),
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur récupération liste EntrepriseCliente', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des entreprises.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* POST /api/entreprise-clientes */
    public function store(StoreEntrepriseClienteRequest $request): JsonResponse
    {
        try {
            // Sécurisé : On utilise uniquement les données validées ($request->validated())
            $entreprise = DB::transaction(fn () =>
                EntrepriseCliente::create($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_CREATED,
                'message' => 'Entreprise cliente créée avec succès.',
                'data'    => $entreprise
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erreur création EntrepriseCliente', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'entreprise.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* GET /api/entreprise-clientes/{entreprise_cliente} */
    public function show(EntrepriseCliente $entreprise_cliente): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'data'    => $entreprise_cliente
        ], Response::HTTP_OK);
    }

    /* PUT/PATCH /api/entreprise-clientes/{entreprise_cliente} */
    public function update(UpdateEntrepriseClienteRequest $request, EntrepriseCliente $entreprise_cliente): JsonResponse
    {
        try {
            DB::transaction(fn () =>
                $entreprise_cliente->update($request->validated())
            );

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Entreprise cliente mise à jour avec succès.',
                'data'    => $entreprise_cliente->fresh()
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour EntrepriseCliente', [
                'id'    => $entreprise_cliente->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'entreprise.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* DELETE /api/entreprise-clientes/{entreprise_cliente} */
    public function destroy(EntrepriseCliente $entreprise_cliente): JsonResponse
    {
        try {
            $entreprise_cliente->delete();

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Entreprise cliente supprimée avec succès.'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erreur suppression EntrepriseCliente', [
                'id'    => $entreprise_cliente->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'entreprise.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}