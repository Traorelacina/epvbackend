<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalerieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image_couverture' => 'nullable|file|image|max:3072',
            'categorie'        => 'required|in:sorties,ceremonies,activites,resultats,autres',
            'annee'            => 'nullable|integer|min:2000|max:2100',
            'publiee'          => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'    => 'Le titre de la galerie est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'categorie.in'       => 'La catégorie sélectionnée est invalide.',
        ];
    }
}