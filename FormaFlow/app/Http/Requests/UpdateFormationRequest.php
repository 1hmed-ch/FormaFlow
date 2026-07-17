<?php

namespace App\Http\Requests;    

use App\Enums\FormationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'intitule' => 'sometimes|required|string|max:255',
            'statut' => ["required", "sometimes", new Enum(FormationStatus::class)],
            'entreprise_id' => 'sometimes|required|exists:entreprise_clientes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'intitule.required' => 'Le titre de la formation est obligatoire.',
            'statut.required' => 'Le statut de la formation est obligatoire.',
            'entreprise_id.required' => 'L\'identifiant de l\'entreprise cliente est obligatoire.',
            'entreprise_id.exists' => 'L\'entreprise cliente spécifiée n\'existe pas.',
        ];
    }
}
