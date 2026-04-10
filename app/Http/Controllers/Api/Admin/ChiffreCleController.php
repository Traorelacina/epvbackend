<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreChiffreCleRequest;
use App\Http\Requests\Admin\UpdateChiffreCleRequest;
use App\Models\ChiffreCle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChiffreCleController extends Controller
{
    /**
     * Liste des chiffres clés
     */
    public function index(): JsonResponse
    {
        $chiffres = ChiffreCle::orderBy('ordre')->get();

        return response()->json(['chiffres' => $chiffres]);
    }

    /**
     * Créer un chiffre clé
     */
    public function store(StoreChiffreCleRequest $request): JsonResponse
    {
        $data          = $request->validated();
        $data['ordre'] = ChiffreCle::max('ordre') + 1;

        $chiffre = ChiffreCle::create($data);

        return response()->json([
            'message' => 'Chiffre clé créé.',
            'chiffre' => $chiffre,
        ], 201);
    }

    /**
     * Mettre à jour un chiffre clé
     */
    public function update(UpdateChiffreCleRequest $request, ChiffreCle $chiffreCle): JsonResponse
    {
        $chiffreCle->update($request->validated());

        return response()->json([
            'message' => 'Chiffre clé mis à jour.',
            'chiffre' => $chiffreCle->fresh(),
        ]);
    }

    /**
     * Supprimer un chiffre clé
     */
    public function destroy(ChiffreCle $chiffreCle): JsonResponse
    {
        $chiffreCle->delete();

        return response()->json(['message' => 'Chiffre clé supprimé.']);
    }

    /**
     * Réordonner les chiffres
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordre'   => 'required|array',
            'ordre.*' => 'integer|exists:chiffres_cles,id',
        ]);

        foreach ($request->ordre as $index => $id) {
            ChiffreCle::where('id', $id)->update(['ordre' => $index + 1]);
        }

        return response()->json(['message' => 'Ordre mis à jour.']);
    }

    /**
     * Basculer l'activation d'un chiffre
     */
    public function toggleActif(ChiffreCle $chiffreCle): JsonResponse
    {
        $chiffreCle->update(['actif' => ! $chiffreCle->actif]);

        return response()->json([
            'message' => $chiffreCle->actif ? 'Chiffre activé.' : 'Chiffre désactivé.',
            'chiffre' => $chiffreCle->fresh(),
        ]);
    }
}