<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }
    public function rules(): array
    {
        return [
            'intitule' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|string',
            'entreprise_id' => 'required|exists:entreprise_clientes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'date_fin.after_or_equal' => 'La date de fin doit être une date postérieure ou égale à la date de début.',
        ];
    }
}