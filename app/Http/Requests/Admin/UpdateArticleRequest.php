<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'            => 'sometimes|string|max:255',
            'extrait'          => 'nullable|string|max:500',
            'contenu'          => 'sometimes|string',
            'image'            => 'nullable|file|image|max:2048',
            'image_alt'        => 'nullable|string|max:255',
            'categorie_id'     => 'nullable|exists:categories,id',
            'statut'           => 'sometimes|in:brouillon,publie,archive',
            'meta_titre'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'date_publication' => 'nullable|date',
        ];
    }
}