<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemoignageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_parent'   => 'required|string|max:150',
            'nom_enfant'   => 'nullable|string|max:150',
            'classe_enfant'=> 'nullable|string|max:100',
            'contenu'      => 'required|string|min:10',
            'note'         => 'nullable|integer|min:1|max:5',
            'photo'        => 'nullable|file|image|max:1024',
            'valide'       => 'boolean',
            'ordre'        => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_parent.required' => 'Le nom du parent est obligatoire.',
            'contenu.required'    => 'Le témoignage est obligatoire.',
            'contenu.min'         => 'Le témoignage doit contenir au moins 10 caractères.',
            'photo.image'         => 'Le fichier doit être une image.',
            'photo.max'           => 'La photo ne doit pas dépasser 1 Mo.',
            'note.min'            => 'La note doit être entre 1 et 5.',
            'note.max'            => 'La note doit être entre 1 et 5.',
        ];
    }
}