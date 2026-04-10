<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTemoignageRequest;
use App\Http\Requests\Admin\UpdateTemoignageRequest;
use App\Models\Temoignage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemoignageController extends Controller
{
    /**
     * Liste des témoignages
     */
    public function index(Request $request): JsonResponse
    {
        $query = Temoignage::query();

        if ($request->filled('valide')) {
            $query->where('valide', filter_var($request->valide, FILTER_VALIDATE_BOOLEAN));
        }

        $temoignages = $query->orderBy('ordre')->latest()->paginate($request->integer('per_page', 15));

        return response()->json($temoignages);
    }

    /**
     * Créer un témoignage
     */
    public function store(StoreTemoignageRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('temoignages', 'public');
        }

        $temoignage = Temoignage::create($data);

        return response()->json([
            'message'     => 'Témoignage créé.',
            'temoignage'  => $temoignage,
        ], 201);
    }

    /**
     * Détail d'un témoignage
     */
    public function show(Temoignage $temoignage): JsonResponse
    {
        return response()->json(['temoignage' => $temoignage]);
    }

    /**
     * Mettre à jour un témoignage
     */
    public function update(UpdateTemoignageRequest $request, Temoignage $temoignage): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($temoignage->photo) {
                Storage::disk('public')->delete($temoignage->photo);
            }
            $data['photo'] = $request->file('photo')->store('temoignages', 'public');
        }

        $temoignage->update($data);

        return response()->json([
            'message'    => 'Témoignage mis à jour.',
            'temoignage' => $temoignage->fresh(),
        ]);
    }

    /**
     * Supprimer un témoignage
     */
    public function destroy(Temoignage $temoignage): JsonResponse
    {
        if ($temoignage->photo) {
            Storage::disk('public')->delete($temoignage->photo);
        }

        $temoignage->delete();

        return response()->json(['message' => 'Témoignage supprimé.']);
    }

    /**
     * Valider un témoignage
     */
    public function valider(Temoignage $temoignage): JsonResponse
    {
        $temoignage->update(['valide' => true]);

        return response()->json(['message' => 'Témoignage validé.', 'temoignage' => $temoignage->fresh()]);
    }

    /**
     * Rejeter un témoignage
     */
    public function rejeter(Temoignage $temoignage): JsonResponse
    {
        $temoignage->update(['valide' => false]);

        return response()->json(['message' => 'Témoignage rejeté.', 'temoignage' => $temoignage->fresh()]);
    }

    /**
     * Réordonner les témoignages
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordre'   => 'required|array',
            'ordre.*' => 'integer|exists:temoignages,id',
        ]);

        foreach ($request->ordre as $index => $id) {
            Temoignage::where('id', $id)->update(['ordre' => $index + 1]);
        }

        return response()->json(['message' => 'Ordre mis à jour.']);
    }
}
