<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreParticipantRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom'              => 'required|string|max:255',
            'prenom'           => 'required|string|max:255',
            'cin'              => 'required|string|max:20|unique:participants,cin',
            'email'            => 'nullable|string|email|max:255|unique:participants,email',
            'numero_cnss'      => 'nullable|string|max:50',
            'fonction_occupee' => 'nullable|string|max:255',
            'telephone'        => 'nullable|string|max:20',
            'categorie_sp'     => 'required|in:C,E,O',
            'entreprise_id'    => 'required|exists:entreprise_clientes,id',
        ];
    }
}
