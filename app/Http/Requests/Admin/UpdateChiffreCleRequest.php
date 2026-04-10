<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChiffreCleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'       => 'sometimes|string|max:150',
            'valeur'      => 'sometimes|string|max:50',
            'suffixe'     => 'nullable|string|max:20',
            'icone'       => 'nullable|string|max:100',
            'couleur'     => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:300',
            'actif'       => 'sometimes|boolean',
        ];
    }
}