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
            'raisonSociale'         => 'sometimes|required|string|max:255',
            'siegeSocial'           => 'sometimes|required|string|max:255',
            'dateCreation'          => 'nullable|date',
            'statutJuridique'       => 'nullable|string|max:100',
            'ice'                   => 'sometimes|required|string|size:15|unique:entreprise_clientes,ice,' . $id, 
            'numCnss'               => 'nullable|string|max:50|unique:entreprise_clientes,numCnss,' . $id,
            'rc'                    => 'nullable|string|max:50|unique:entreprise_clientes,rc,' . $id,
            'if'                    => 'sometimes|required|string|max:50|unique:entreprise_clientes,if,' . $id,   
            'patente'               => 'nullable|string|max:50|unique:entreprise_clientes,patente,' . $id,
            'secteurActivite'       => 'sometimes|required|string|max:255',
            'activite'              => 'nullable|string|max:255',
            'regionAffiliationCnss' => 'nullable|string|max:255',
            'effectifTotal'         => 'nullable|string|max:50',
            'telephone'             => 'nullable|string|max:50',
            'fax'                   => 'nullable|string|max:50',
            'email'                 => 'sometimes|required|email|max:255|unique:entreprise_clientes,email,' . $id, 
            'contactRef'            => 'nullable|string|max:255',
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