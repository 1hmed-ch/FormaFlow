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
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'lieu'         => 'nullable|string|max:255',
            'effectif_max' => 'required|integer|min:1',
            'theme_id'     => 'required|exists:themes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
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

            $dateDebut = $this->input('date_debut');
            $dateFin   = $this->input('date_fin');

            if ($dateDebut < $theme->formation->date_debut || $dateFin > $theme->formation->date_fin) {
                $validator->errors()->add(
                    'date_debut',
                    'Les dates du groupe doivent être comprises dans la période de la formation ('
                    . $theme->formation->date_debut . ' au ' . $theme->formation->date_fin . ').'
                );
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
