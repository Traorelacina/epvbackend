<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEquipeRequest;
use App\Http\Requests\Admin\UpdateEquipeRequest;
use App\Models\Equipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipeController extends Controller
{
    /**
     * Liste des membres de l'équipe
     */
    public function index(Request $request): JsonResponse
    {
        $query = Equipe::query();

        if ($request->filled('affiche')) {
            $query->where('affiche', filter_var($request->affiche, FILTER_VALIDATE_BOOLEAN));
        }

        $equipe = $query->orderBy('ordre')->get();

        return response()->json(['equipe' => $equipe]);
    }

    /**
     * Détail d'un membre
     */
    public function show(Equipe $equipe): JsonResponse
    {
        return response()->json(['membre' => $equipe]);
    }

    /**
     * Créer un membre
     */
    public function store(StoreEquipeRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('equipe', 'public');
        }

        $data['ordre'] = Equipe::max('ordre') + 1;

        $membre = Equipe::create($data);

        return response()->json([
            'message' => 'Membre ajouté.',
            'membre'  => $membre,
        ], 201);
    }

    /**
     * Mettre à jour un membre
     */
    public function update(UpdateEquipeRequest $request, Equipe $equipe): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($equipe->photo) {
                Storage::disk('public')->delete($equipe->photo);
            }
            $data['photo'] = $request->file('photo')->store('equipe', 'public');
        }

        $equipe->update($data);

        return response()->json([
            'message' => 'Membre mis à jour.',
            'membre'  => $equipe->fresh(),
        ]);
    }

    /**
     * Supprimer un membre
     */
    public function destroy(Equipe $equipe): JsonResponse
    {
        if ($equipe->photo) {
            Storage::disk('public')->delete($equipe->photo);
        }

        $equipe->delete();

        return response()->json(['message' => 'Membre supprimé.']);
    }

    /**
     * Réordonner les membres
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordre'   => 'required|array',
            'ordre.*' => 'integer|exists:equipe,id',
        ]);

        foreach ($request->ordre as $index => $id) {
            Equipe::where('id', $id)->update(['ordre' => $index + 1]);
        }

        return response()->json(['message' => 'Ordre mis à jour.']);
    }

    /**
     * Basculer visibilité d'un membre
     */
    public function toggleAffiche(Equipe $equipe): JsonResponse
    {
        $equipe->update(['affiche' => ! $equipe->affiche]);

        return response()->json([
            'message' => $equipe->affiche ? 'Membre affiché.' : 'Membre masqué.',
            'membre'  => $equipe->fresh(),
        ]);
    }
}