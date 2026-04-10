<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreNiveauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => 'required|string|max:100|unique:niveaux,nom',
            'age_min'     => 'nullable|string|max:50',
            'age_max'     => 'nullable|string|max:50',
            'description' => 'required|string',
            'programmes'  => 'nullable|string',
            'icone'       => 'nullable|string|max:100',
            'couleur'     => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ordre'       => 'nullable|integer|min:0',
            'actif'       => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'         => 'Le nom du niveau est obligatoire.',
            'nom.unique'           => 'Ce niveau existe déjà.',
            'description.required' => 'La description est obligatoire.',
        ];
    }
}
