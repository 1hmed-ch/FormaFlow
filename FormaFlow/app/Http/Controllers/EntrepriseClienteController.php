<?php

namespace App\Http\Controllers;

use App\Exceptions\SuppressionBloqueeException;
use App\Http\Requests\StoreEntrepriseClienteRequest;
use App\Http\Requests\UpdateEntrepriseClienteRequest;
use App\Models\EntrepriseCliente;
use App\Models\Gerant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntrepriseClienteController extends Controller
{
    /* GET /api/entreprise-clientes */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

            $entreprises = EntrepriseCliente::with('gerant')->paginate($perPage);

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
            $entreprise = DB::transaction(function () use ($request) {
                $validated = $request->validated();

                // On crée le Gérant en premier
                $gerant = Gerant::create([
                    'nom'      => $validated['gerant_nom'],
                    'prenom'   => $validated['gerant_prenom'],
                    'fonction' => $validated['gerant_fonction'],
                    'cin'      => $validated['gerant_cin'],
                    'genre'    => $validated['gerant_genre'],
                ]);

                // On sépare les champs du gérant pour insérer uniquement ceux de l'entreprise
                $entrepriseData = collect($validated)
                    ->except(['gerant_nom', 'gerant_prenom', 'gerant_fonction', 'gerant_cin','gerant_genre'])
                    ->merge(['gerant_id' => $gerant->id])
                    ->toArray();

                return EntrepriseCliente::create($entrepriseData);
            });

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_CREATED,
                'message' => 'Entreprise cliente et son gérant créés avec succès.',
                'data'    => $entreprise->load('gerant')
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
        // Ajout de load('gerant') pour inclure l'objet imbriqué
        return response()->json([
            'success' => true,
            'status'  => Response::HTTP_OK,
            'data'    => $entreprise_cliente->load('gerant')
        ], Response::HTTP_OK);
    }

    /* PUT/PATCH /api/entreprise-clientes/{entreprise_cliente} */
    public function update(UpdateEntrepriseClienteRequest $request, EntrepriseCliente $entreprise_cliente): JsonResponse
    {
        try {
            DB::transaction(function () use ($request, $entreprise_cliente) {

                $entreprise_cliente->update($request->validated());

                if ($entreprise_cliente->gerant) {
                    $entreprise_cliente->gerant->update([
                        'nom'      => $request->input('gerant_nom', $entreprise_cliente->gerant->nom),
                        'prenom'   => $request->input('gerant_prenom', $entreprise_cliente->gerant->prenom),
                        'fonction' => $request->input('gerant_fonction', $entreprise_cliente->gerant->fonction),
                        'cin'      => $request->input('gerant_cin', $entreprise_cliente->gerant->cin),
                        'genre'    => $request->input('gerant_genre', $entreprise_cliente->gerant->genre),
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'status'  => Response::HTTP_OK,
                'message' => 'Entreprise cliente et les informations du gérant mises à jour avec succès.',
                'data'    => $entreprise_cliente->fresh()->load('gerant')
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
        } catch (SuppressionBloqueeException $e) {
            return response()->json([
                'success' => false,
                'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
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

    // Modèle 6 se génère désormais par formation :
    // GET /api/formations/{formation}/documents/modele-6?annee=2026
    // Voir FormationController::genererModele6().
}
