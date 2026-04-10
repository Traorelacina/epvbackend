<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut'  => 'required|date',
            'date_fin'    => 'nullable|date|after_or_equal:date_debut',
            'type'        => 'required|in:vacances,rentree,examen,evenement,fermeture,autre',
            'concerne'    => 'nullable|string|max:150',
            'couleur'     => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'actif'       => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'      => 'Le titre est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
            'type.required'       => 'Le type d\'événement est obligatoire.',
            'type.in'             => 'Le type sélectionné est invalide.',
        ];
    }
}