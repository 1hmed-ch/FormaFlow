<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use App\Enums\GerantGender; 
use Illuminate\Validation\Rules\Enum;

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
            'raison_sociale'          => 'required|string|max:255',
            'siege_social'            => 'required|string|max:255',
            'ville'                   => 'required|string|max:255',
            'date_creation'           => 'nullable|date',
            'statut_juridique'        => 'nullable|string|max:100',
            'ice'                     => 'required|string|size:15|unique:entreprise_clientes,ice', // Required & Unique 
            'num_cnss'                => 'nullable|string|max:50|unique:entreprise_clientes,num_cnss',
            'montant_tfp'             => 'nullable|numeric|min:0',
            'deja_depose_giac'        => 'nullable|boolean',
            'nom_ancien_giac'         => 'nullable|string|max:255',
            'date_depot_ancien_giac'  => 'nullable|date:Y-m-d',
            'rc'                      => 'nullable|string|max:50|unique:entreprise_clientes,rc',
            'if'                      => 'required|string|max:50|unique:entreprise_clientes,if',   // Required & Unique Strict fiscal
            'patente'                 => 'nullable|string|max:50|unique:entreprise_clientes,patente',
            'secteur_activite'        => 'required|string|max:255',
            'activite'                => 'nullable|string|max:255',
            'region_affiliation_cnss' => 'nullable|string|max:255',
            'effectif_total'          => 'nullable|integer|min:1',
            'effectif_cadre'          => 'nullable|integer|min:0',
            'effectif_cadre_moyen'    => 'nullable|integer|min:0',
            'effectif_agent_qualifie' => 'nullable|integer|min:0',
            'effectif_agent_sans_qualification' => 'nullable|integer|min:0',
            'effectif_agent_occasionnel' => 'nullable|integer|min:0',
            'telephone'               => 'nullable|string|max:50',
            'fax'                     => 'nullable|string|max:50',
            'email'                   => 'required|email|max:255|unique:entreprise_clientes,email', // Required & Unique pour la communication
            'contact_ref'             => 'nullable|string|max:255',
            'gerant_nom'              => 'required|string|max:255',
            'gerant_prenom'           => 'required|string|max:255',
            'gerant_fonction'         => 'required|string|max:255', 
            'gerant_cin'              => 'required|string|max:20|unique:gerants,cin',
            'gerant_email'            => 'required|email|max:255|unique:gerants,email',
            'gerant_genre'            => ['required', new Enum(GerantGender::class)],
            'gerant_telephone'        => ['nullable', 'string', 'max:20'],
            'cheque_banque'           => 'nullable|string|max:255',
            'cheque_numero'           => 'nullable|string|max:50',
            'cheque_date'             => 'nullable|date:Y-m-d',
            'gmail_login_ofppt'       => ['nullable', 'email', 'max:255'],
            'gmail_ofppt_mdp'         => ['nullable', 'string', 'max:255'],
            'ofppt_mdp'               => ['nullable', 'string', 'max:255'],
            


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
