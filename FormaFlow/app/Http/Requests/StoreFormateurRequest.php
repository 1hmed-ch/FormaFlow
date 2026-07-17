<?php

namespace App\Http\Requests;

use App\Enums\FormateurStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreFormateurRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'unique:formateurs|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'specialite' => 'nullable|string|max:255',
            'statut' => ['required', new Enum(FormateurStatus::class)],
        ];
    }
}
