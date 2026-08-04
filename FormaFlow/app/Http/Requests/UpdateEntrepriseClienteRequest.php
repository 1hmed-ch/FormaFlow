<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use App\Enums\GerantGender; 
use Illuminate\Validation\Rules\Enum;
use App\Enums\DemandeFinancementStatus;
class UpdateEntrepriseClienteRequest extends FormRequest
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
        $entrepriseCliente = $this->route('entreprise_cliente');
        $id = is_object($entrepriseCliente) ? $entrepriseCliente->id : $entrepriseCliente;

        $gerantId = null;
        if (is_object($entrepriseCliente)) {
            $gerantId = $entrepriseCliente->gerant_id;
        } elseif ($id) {
            $entreprise = \App\Models\EntrepriseCliente::find($id);
            $gerantId = $entreprise ? $entreprise->gerant_id : null;
        }

        return [
            'raison_sociale'         => 'sometimes|required|string|max:255',
            'siege_social'           => 'sometimes|required|string|max:255',
            'ville'                  => 'sometimes|required|string|max:255',
            'date_creation'          => 'nullable|date',
            'statut_juridique'       => 'nullable|string|max:100',
            'ice'                    => 'sometimes|required|string|size:15|unique:entreprise_clientes,ice,' . $id,
            'num_cnss'               => 'nullable|string|max:50|unique:entreprise_clientes,num_cnss,' . $id,
            'montant_tfp'            => 'nullable|numeric|min:0',
            'deja_depose_giac'       => 'nullable|boolean',
            'nom_ancien_giac'        => 'nullable|string|max:255',
            'date_depot_ancien_giac' => 'nullable|date:Y-m-d',
            'rc'                     => 'nullable|string|max:50|unique:entreprise_clientes,rc,' . $id,
            'if'                     => 'sometimes|required|string|max:50|unique:entreprise_clientes,if,' . $id,
            'patente'                => 'nullable|string|max:50|unique:entreprise_clientes,patente,' . $id,
            'secteur_activite'       => 'sometimes|required|string|max:255',
            'activite'               => 'nullable|string|max:255',
            'region_affiliation_cnss' => 'nullable|string|max:255',
            'effectif_total'          => 'nullable|integer|min:1',
            'effectif_cadre'          => 'nullable|integer|min:0',
            'effectif_cadre_moyen'    => 'nullable|integer|min:0',
            'effectif_agent_qualifie' => 'nullable|integer|min:0',
            'effectif_agent_sans_qualification' => 'nullable|integer|min:0',
            'effectif_agent_occasionnel' => 'nullable|integer|min:0',
            'telephone'               => 'nullable|string|max:50',
            'fax'                     => 'nullable|string|max:50',
            'email'                   => 'sometimes|required|email|max:255|unique:entreprise_clientes,email,' . $id,
            'contact_ref'             => 'nullable|string|max:255',
            'gerant_nom'              => 'sometimes|required|string|max:255',
            'gerant_prenom'           => 'sometimes|required|string|max:255',
            'gerant_fonction'         => 'sometimes|required|string|max:255',
            'gerant_genre'            => ['sometimes', new Enum(GerantGender::class)],
            'gerant_cin'              => 'sometimes|required|string|max:50|unique:gerants,cin,' . $gerantId,
            'gerant_telephone'        => ['nullable', 'string', 'max:20'],
            'gerant_email'            => 'sometimes|required|email|max:255|unique:gerants,email,' . $gerantId,
            'cheque_banque'           => 'nullable|string|max:255',
            'cheque_numero'           => 'nullable|string|max:50',
            'cheque_date'             => 'nullable|date:Y-m-d',
            'gmail_login_ofppt'       => ['nullable', 'email', 'max:255'],
            'gmail_ofppt_mdp'         => ['nullable', 'string', 'max:255'],
            'ofppt_mdp'               => ['nullable', 'string', 'max:255'],
            'statut_demande_financement' => ['nullable', Rule::enum(DemandeFinancementStatus::class)],
    
            
            
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
