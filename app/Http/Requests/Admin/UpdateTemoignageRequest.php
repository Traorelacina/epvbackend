<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemoignageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_parent'    => 'sometimes|string|max:150',
            'nom_enfant'    => 'nullable|string|max:150',
            'classe_enfant' => 'nullable|string|max:100',
            'contenu'       => 'sometimes|string|min:10',
            'note'          => 'nullable|integer|min:1|max:5',
            'photo'         => 'nullable|file|image|max:1024',
            'valide'        => 'sometimes|boolean',
            'ordre'         => 'nullable|integer|min:0',
        ];
    }
}