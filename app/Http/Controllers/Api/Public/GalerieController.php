<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Galerie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalerieController extends Controller
{
    /**
     * Liste des galeries publiées
     */
    public function index(Request $request): JsonResponse
    {
        $query = Galerie::withCount('medias')
            ->where('publiee', true);

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        $galeries = $query->latest()->paginate($request->integer('per_page', 12));

        // Années disponibles pour le filtre
        $annees = Galerie::where('publiee', true)
            ->whereNotNull('annee')
            ->distinct()
            ->orderByDesc('annee')
            ->pluck('annee');

        return response()->json([
            'galeries' => $galeries,
            'annees'   => $annees,
        ]);
    }

    /**
     * Détail d'une galerie avec ses médias
     */
    public function show(string $slug): JsonResponse
    {
        $galerie = Galerie::with(['medias' => fn ($q) => $q->orderBy('ordre')])
            ->where('slug', $slug)
            ->where('publiee', true)
            ->firstOrFail();

        return response()->json(['galerie' => $galerie]);
    }
}