<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNiveauRequest;
use App\Http\Requests\Admin\UpdateNiveauRequest;
use App\Models\Niveau;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NiveauController extends Controller
{
    /**
     * Liste des niveaux
     */
    public function index(): JsonResponse
    {
        $niveaux = Niveau::with('fraisScolarite')
            ->orderBy('ordre')
            ->get();

        return response()->json(['niveaux' => $niveaux]);
    }

    /**
     * Détail d'un niveau
     */
    public function show(Niveau $niveau): JsonResponse
    {
        $niveau->load('fraisScolarite');

        return response()->json(['niveau' => $niveau]);
    }

    /**
     * Créer un niveau
     */
    public function store(StoreNiveauRequest $request): JsonResponse
    {
        $data         = $request->validated();
        $data['slug'] = Str::slug($data['nom']);

        $niveau = Niveau::create($data);

        return response()->json([
            'message' => 'Niveau créé avec succès.',
            'niveau'  => $niveau,
        ], 201);
    }

    /**
     * Mettre à jour un niveau
     */
    public function update(UpdateNiveauRequest $request, Niveau $niveau): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['nom'])) {
            $data['slug'] = Str::slug($data['nom']);
        }

        $niveau->update($data);

        return response()->json([
            'message' => 'Niveau mis à jour.',
            'niveau'  => $niveau->fresh('fraisScolarite'),
        ]);
    }

    /**
     * Supprimer un niveau
     */
    public function destroy(Niveau $niveau): JsonResponse
    {
        $niveau->delete();

        return response()->json(['message' => 'Niveau supprimé.']);
    }

    /**
     * Réordonner les niveaux
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordre'   => 'required|array',
            'ordre.*' => 'integer|exists:niveaux,id',
        ]);

        foreach ($request->ordre as $index => $niveauId) {
            Niveau::where('id', $niveauId)->update(['ordre' => $index + 1]);
        }

        return response()->json(['message' => 'Ordre mis à jour.']);
    }

    // ─── Frais de scolarité ───────────────────────────────────────

    /**
     * Ajouter un frais à un niveau
     */
    public function addFrais(Request $request, Niveau $niveau): JsonResponse
    {
        $request->validate([
            'libelle'      => 'required|string|max:255',
            'montant'      => 'required|numeric|min:0',
            'periodicite'  => 'required|in:unique,mensuel,trimestriel,annuel',
            'notes'        => 'nullable|string',
        ]);

        $frais = $niveau->fraisScolarite()->create($request->only('libelle', 'montant', 'periodicite', 'notes'));

        return response()->json([
            'message' => 'Frais ajouté.',
            'frais'   => $frais,
        ], 201);
    }

    /**
     * Mettre à jour un frais
     */
    public function updateFrais(Request $request, Niveau $niveau, int $fraisId): JsonResponse
    {
        $request->validate([
            'libelle'      => 'sometimes|string|max:255',
            'montant'      => 'sometimes|numeric|min:0',
            'periodicite'  => 'sometimes|in:unique,mensuel,trimestriel,annuel',
            'notes'        => 'nullable|string',
            'actif'        => 'sometimes|boolean',
        ]);

        $frais = $niveau->fraisScolarite()->findOrFail($fraisId);
        $frais->update($request->only('libelle', 'montant', 'periodicite', 'notes', 'actif'));

        return response()->json(['message' => 'Frais mis à jour.', 'frais' => $frais->fresh()]);
    }

    /**
     * Supprimer un frais
     */
    public function deleteFrais(Niveau $niveau, int $fraisId): JsonResponse
    {
        $frais = $niveau->fraisScolarite()->findOrFail($fraisId);
        $frais->delete();

        return response()->json(['message' => 'Frais supprimé.']);
    }
}