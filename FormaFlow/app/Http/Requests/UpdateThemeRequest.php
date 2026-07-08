<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThemeRequest extends FormRequest
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
            'intitule'     => 'sometimes|required|string|max:255',
            'duree_prevue' => 'sometimes|required|integer|min:1',
            'objectifs'    => 'nullable|string',
            'formation_id' => 'sometimes|required|integer|exists:formations,id',
            'formateur_id' => 'sometimes|required|integer|exists:formateurs,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'intitule.required'     => 'Le titre du thème ne peut pas être vide.',
            'duree_prevue.required' => 'La durée prévue ne peut pas être vide.',
            'duree_prevue.integer'  => 'La durée doit être un nombre entier.',
            'formation_id.exists'   => 'La formation spécifiée n\'existe pas.',
            'formateur_id.exists'   => 'Le formateur spécifié n\'existe pas.',
        ];
    }
}