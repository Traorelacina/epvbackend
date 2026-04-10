<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalendrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'date_debut'  => 'sometimes|date',
            'date_fin'    => 'nullable|date|after_or_equal:date_debut',
            'type'        => 'sometimes|in:vacances,rentree,examen,evenement,fermeture,autre',
            'concerne'    => 'nullable|string|max:150',
            'couleur'     => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'actif'       => 'sometimes|boolean',
        ];
    }
}