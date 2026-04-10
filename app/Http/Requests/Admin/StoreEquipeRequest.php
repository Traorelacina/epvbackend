<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'        => 'required|string|max:150',
            'prenom'     => 'required|string|max:150',
            'role'       => 'required|string|max:150',
            'classe'     => 'nullable|string|max:100',
            'biographie' => 'nullable|string',
            'photo'      => 'nullable|file|image|max:1024',
            'email'      => 'nullable|email|max:255',
            'affiche'    => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'    => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'role.required'   => 'Le rôle est obligatoire.',
            'photo.image'     => 'Le fichier doit être une image.',
            'photo.max'       => 'La photo ne doit pas dépasser 1 Mo.',
        ];
    }
}