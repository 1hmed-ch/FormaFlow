<?php

namespace App\Http\Requests;

use App\Models\Theme;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreGroupeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle'      => 'required|string|max:255',
            'lieu'         => 'nullable|string|max:255',
            'effectif_max' => 'required|integer|min:1',
            'theme_id'     => 'required|exists:themes,id',
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

    /**
     * Règle métier CSF 2.3.6 : les dates du groupe doivent être comprises
     * dans l'intervalle de la Formation parente (via Theme -> Formation),
     * puisque les dates du Thème sont elles-mêmes dérivées des Groupes
     * (cf. remarque envoyée à l'encadrant sur cette ambiguïté du CDC).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $themeId = $this->input('theme_id');
            if (!$themeId) {
                return;
            }

            $theme = Theme::with('formation')->find($themeId);
            if (!$theme || !$theme->formation) {
                return;
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Les données fournies sont invalides.',
            'errors'  => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
