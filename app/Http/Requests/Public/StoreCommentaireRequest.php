<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'     => 'required|string|max:150',
            'email'   => 'required|email|max:255',
            'contenu' => 'required|string|min:5|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'     => 'Votre nom est obligatoire.',
            'email.required'   => 'Votre adresse email est obligatoire.',
            'email.email'      => 'L\'adresse email n\'est pas valide.',
            'contenu.required' => 'Le commentaire est obligatoire.',
            'contenu.min'      => 'Le commentaire doit contenir au moins 5 caractères.',
            'contenu.max'      => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ];
    }
}