<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreChiffreCleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'       => 'required|string|max:150',
            'valeur'      => 'required|string|max:50',
            'suffixe'     => 'nullable|string|max:20',
            'icone'       => 'nullable|string|max:100',
            'couleur'     => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:300',
            'actif'       => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'label.required'  => 'Le libellé est obligatoire.',
            'valeur.required' => 'La valeur est obligatoire.',
        ];
    }
}