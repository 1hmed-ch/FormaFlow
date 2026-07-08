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
            'date_debut'   => 'sometimes|required|date',
            'date_fin'     => 'sometimes|required|date|after_or_equal:date_debut',
            'lieu'         => 'nullable|string|max:255',
            'effectif_max' => 'sometimes|required|integer|min:1',
            'theme_id'     => 'sometimes|required|exists:themes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
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
