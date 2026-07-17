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
             'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
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
            'date_fin.after_or_equal' => 'La date de fin doit être une date postérieure ou égale à la date de début.',
            'formation_id.exists'   => 'La formation spécifiée n\'existe pas.',
            'formateur_id.exists'   => 'Le formateur spécifié n\'existe pas.',
        ];
    }
}