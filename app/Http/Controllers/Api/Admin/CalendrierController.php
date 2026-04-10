<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCalendrierRequest;
use App\Http\Requests\Admin\UpdateCalendrierRequest;
use App\Models\Calendrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendrierController extends Controller
{
    /**
     * Liste des événements du calendrier
     */
    public function index(Request $request): JsonResponse
    {
        $query = Calendrier::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('actif')) {
            $query->where('actif', filter_var($request->actif, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('annee')) {
            $query->whereYear('date_debut', $request->annee);
        }

        $calendrier = $query->orderBy('date_debut')->get();

        return response()->json(['calendrier' => $calendrier]);
    }

    /**
     * Créer un événement
     */
    public function store(StoreCalendrierRequest $request): JsonResponse
    {
        $evenement = Calendrier::create($request->validated());

        return response()->json([
            'message'    => 'Événement ajouté au calendrier.',
            'evenement'  => $evenement,
        ], 201);
    }

    /**
     * Détail d'un événement
     */
    public function show(Calendrier $calendrier): JsonResponse
    {
        return response()->json(['evenement' => $calendrier]);
    }

    /**
     * Mettre à jour un événement
     */
    public function update(UpdateCalendrierRequest $request, Calendrier $calendrier): JsonResponse
    {
        $calendrier->update($request->validated());

        return response()->json([
            'message'   => 'Événement mis à jour.',
            'evenement' => $calendrier->fresh(),
        ]);
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Calendrier $calendrier): JsonResponse
    {
        $calendrier->delete();

        return response()->json(['message' => 'Événement supprimé.']);
    }

    /**
     * Basculer l'activation d'un événement
     */
    public function toggleActif(Calendrier $calendrier): JsonResponse
    {
        $calendrier->update(['actif' => ! $calendrier->actif]);

        return response()->json([
            'message'   => $calendrier->actif ? 'Événement activé.' : 'Événement désactivé.',
            'evenement' => $calendrier->fresh(),
        ]);
    }
}