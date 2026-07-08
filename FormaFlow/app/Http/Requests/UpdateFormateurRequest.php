<?php

namespace App\Http\Requests;

use App\Enums\FormateurStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateFormateurRequest extends FormRequest
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
            'nom'        => 'sometimes|required|string|max:255',
            'prenom'     => 'sometimes|required|string|max:255',
            'email'      => 'nullable|email|max:255|unique:formateurs,email,' . $this->formateur->id,
            'telephone'  => 'nullable|string|max:20',
            'specialite' => 'nullable|string|max:255',
            'statut'     => ['sometimes', 'required', new Enum(FormateurStatus::class)],
        ];
    }
}
