<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'            => 'required|string|max:255',
            'extrait'          => 'nullable|string|max:500',
            'contenu'          => 'required|string',
            'image'            => 'nullable|file|image|max:2048',
            'image_alt'        => 'nullable|string|max:255',
            'categorie_id'     => 'nullable|exists:categories,id',
            'statut'           => 'required|in:brouillon,publie,archive',
            'meta_titre'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'date_publication' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'   => 'Le titre de l\'article est obligatoire.',
            'contenu.required' => 'Le contenu de l\'article est obligatoire.',
            'statut.required'  => 'Le statut est obligatoire.',
            'statut.in'        => 'Le statut est invalide.',
            'image.image'      => 'Le fichier doit être une image.',
            'image.max'        => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }
}
