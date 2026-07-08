<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreEntrepriseClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
   public function rules(): array
    {
        return [
            'raisonSociale'         => 'required|string|max:255',
            'siegeSocial'           => 'required|string|max:255',
            'dateCreation'          => 'nullable|date',
            'statutJuridique'       => 'nullable|string|max:100',
            'ice'                   => 'required|string|size:15|unique:entreprise_clientes,ice', // Required & Unique (ICE fih exactly 15 chiffres)
            'numCnss'               => 'nullable|string|max:50|unique:entreprise_clientes,numCnss',
            'rc'                    => 'nullable|string|max:50|unique:entreprise_clientes,rc',
            'if'                    => 'required|string|max:50|unique:entreprise_clientes,if',   // Required & Unique Strict fiscal
            'patente'               => 'nullable|string|max:50|unique:entreprise_clientes,patente',
            'secteurActivite'       => 'required|string|max:255',
            'activite'              => 'nullable|string|max:255',
            'regionAffiliationCnss' => 'nullable|string|max:255',
            'effectifTotal'         => 'nullable|string|max:50',
            'telephone'             => 'nullable|string|max:50',
            'fax'                   => 'nullable|string|max:50',
            'email'                 => 'required|email|max:255|unique:entreprise_clientes,email', // Required & Unique pour la communication
            'contactRef'            => 'nullable|string|max:255',
        ];
    }

    /**
     * Gérer les erreurs de validation au format JSON
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Les données fournies sont invalides.',
            'errors'  => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
