<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNiveauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $niveauId = $this->route('niveau')?->id;

        return [
            'nom'         => 'sometimes|string|max:100|unique:niveaux,nom,' . $niveauId,
            'age_min'     => 'nullable|string|max:50',
            'age_max'     => 'nullable|string|max:50',
            'description' => 'sometimes|string',
            'programmes'  => 'nullable|string',
            'icone'       => 'nullable|string|max:100',
            'couleur'     => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ordre'       => 'nullable|integer|min:0',
            'actif'       => 'sometimes|boolean',
        ];
    }
}