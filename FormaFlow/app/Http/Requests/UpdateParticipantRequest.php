<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateParticipantRequest extends FormRequest
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
            'nom'             => 'sometimes|required|string|max:255',
            'prenom'          => 'sometimes|required|string|max:255',
            'cin'             => 'sometimes|required|string|max:20|unique:participants,cin,' . $this->participant->id,
            'email'           => 'sometimes|nullable|string|email|max:255|unique:participants,email,' . $this->participant->id,
            'numero_cnss'     => 'nullable|string|max:50',
            'fonction_occupee'=> 'nullable|string|max:255',
            'telephone'       => 'nullable|string|max:20',
            'categorie_sp'    => 'sometimes|required|in:C,E,O',
            'entreprise_id'   => 'sometimes|required|exists:entreprise_clientes,id',
        ];
    }
}
