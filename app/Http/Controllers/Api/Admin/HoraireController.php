<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHoraireRequest;
use App\Http\Requests\Admin\UpdateHoraireRequest;
use App\Models\Horaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HoraireController extends Controller
{
    /**
     * Liste des horaires
     */
    public function index(): JsonResponse
    {
        $horaires = Horaire::orderBy('ordre')->get();

        return response()->json(['horaires' => $horaires]);
    }

    /**
     * Créer un horaire
     */
    public function store(StoreHoraireRequest $request): JsonResponse
    {
        $data          = $request->validated();
        $data['ordre'] = Horaire::max('ordre') + 1;

        $horaire = Horaire::create($data);

        return response()->json([
            'message' => 'Horaire créé.',
            'horaire' => $horaire,
        ], 201);
    }

    /**
     * Mettre à jour un horaire
     */
    public function update(UpdateHoraireRequest $request, Horaire $horaire): JsonResponse
    {
        $horaire->update($request->validated());

        return response()->json([
            'message' => 'Horaire mis à jour.',
            'horaire' => $horaire->fresh(),
        ]);
    }

    /**
     * Supprimer un horaire
     */
    public function destroy(Horaire $horaire): JsonResponse
    {
        $horaire->delete();

        return response()->json(['message' => 'Horaire supprimé.']);
    }

    /**
     * Réordonner les horaires
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordre'   => 'required|array',
            'ordre.*' => 'integer|exists:horaires,id',
        ]);

        foreach ($request->ordre as $index => $id) {
            Horaire::where('id', $id)->update(['ordre' => $index + 1]);
        }

        return response()->json(['message' => 'Ordre mis à jour.']);
    }
}