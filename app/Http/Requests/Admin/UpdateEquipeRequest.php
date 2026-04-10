<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'        => 'sometimes|string|max:150',
            'prenom'     => 'sometimes|string|max:150',
            'role'       => 'sometimes|string|max:150',
            'classe'     => 'nullable|string|max:100',
            'biographie' => 'nullable|string',
            'photo'      => 'nullable|file|image|max:1024',
            'email'      => 'nullable|email|max:255',
            'affiche'    => 'sometimes|boolean',
        ];
    }
}