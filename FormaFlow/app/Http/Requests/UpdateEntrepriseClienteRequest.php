<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateEntrepriseClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Requis pour activer la requête via l'API
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $entrepriseCliente = $this->route('entreprise_cliente');
        $id = is_object($entrepriseCliente) ? $entrepriseCliente->id : $entrepriseCliente;

        return [
            'raison_sociale'         => 'sometimes|required|string|max:255',
            'siege_social'           => 'sometimes|required|string|max:255',
            'date_creation'          => 'nullable|date',
            'statut_juridique'       => 'nullable|string|max:100',
            'ice'                   => 'sometimes|required|string|size:15|unique:entreprise_clientes,ice,' . $id, 
            'num_cnss'               => 'nullable|string|max:50|unique:entreprise_clientes,num_cnss,' . $id,
            'rc'                    => 'nullable|string|max:50|unique:entreprise_clientes,rc,' . $id,
            'if'                    => 'sometimes|required|string|max:50|unique:entreprise_clientes,if,' . $id,   
            'patente'               => 'nullable|string|max:50|unique:entreprise_clientes,patente,' . $id,
            'secteur_activite'       => 'sometimes|required|string|max:255',
            'activite'              => 'nullable|string|max:255',
            'region_affiliation_cnss' => 'nullable|string|max:255',
            'effectif_total'         => 'nullable|string|max:50',
            'telephone'             => 'nullable|string|max:50',
            'fax'                   => 'nullable|string|max:50',
            'email'                 => 'sometimes|required|email|max:255|unique:entreprise_clientes,email,' . $id, 
            'contact_ref'            => 'nullable|string|max:255',
        ];
    }

    /* Gérer les erreurs de validation au format JSON  */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Échec de la validation des données lors de la mise à jour.',
            'errors'  => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}