<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    /**
     * Liste des catégories avec compte d'articles
     */
    public function index(): JsonResponse
    {
        $categories = Categorie::withCount('articles')->orderBy('nom')->get();

        return response()->json(['categories' => $categories]);
    }

    /**
     * Créer une catégorie
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom'     => 'required|string|max:100|unique:categories,nom',
            'couleur' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $categorie = Categorie::create([
            'nom'     => $request->nom,
            'slug'    => Str::slug($request->nom),
            'couleur' => $request->couleur ?? '#1B4F8A',
        ]);

        return response()->json([
            'message'   => 'Catégorie créée.',
            'categorie' => $categorie,
        ], 201);
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, Categorie $categorie): JsonResponse
    {
        $request->validate([
            'nom'     => 'sometimes|string|max:100|unique:categories,nom,' . $categorie->id,
            'couleur' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $data = $request->only('nom', 'couleur');

        if (isset($data['nom'])) {
            $data['slug'] = Str::slug($data['nom']);
        }

        $categorie->update($data);

        return response()->json([
            'message'   => 'Catégorie mise à jour.',
            'categorie' => $categorie->fresh(),
        ]);
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(Categorie $categorie): JsonResponse
    {
        if ($categorie->articles()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer une catégorie ayant des articles associés.',
            ], 422);
        }

        $categorie->delete();

        return response()->json(['message' => 'Catégorie supprimée.']);
    }
}