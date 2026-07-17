<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateGroupeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle'      => 'sometimes|required|string|max:255',
            'lieu'         => 'nullable|string|max:255',
            'effectif_max' => 'sometimes|required|integer|min:1',
            'theme_id'     => 'sometimes|required|exists:themes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required'      => 'Le libellé du groupe est obligatoire.',
            'effectif_max.required' => 'L\'effectif maximum du groupe est obligatoire.',
            'effectif_max.integer'  => 'L\'effectif maximum doit être un nombre entier.',
            'effectif_max.min'      => 'L\'effectif maximum doit être au moins de 1.',
            'theme_id.required'     => 'L\'identifiant du thème est obligatoire.',
            'theme_id.exists'       => 'Le thème spécifié n\'existe pas.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Échec de la validation des données lors de la mise à jour.',
            'errors'  => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
