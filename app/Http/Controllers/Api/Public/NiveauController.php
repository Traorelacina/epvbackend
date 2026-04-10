<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use Illuminate\Http\JsonResponse;

class NiveauController extends Controller
{
    /**
     * Liste des niveaux scolaires actifs
     */
    public function index(): JsonResponse
    {
        $niveaux = Niveau::with(['fraisScolarite' => fn ($q) => $q->where('actif', true)])
            ->where('actif', true)
            ->orderBy('ordre')
            ->get();

        return response()->json(['niveaux' => $niveaux]);
    }

    /**
     * Détail d'un niveau
     */
    public function show(string $slug): JsonResponse
    {
        $niveau = Niveau::with(['fraisScolarite' => fn ($q) => $q->where('actif', true)])
            ->where('slug', $slug)
            ->where('actif', true)
            ->firstOrFail();

        return response()->json(['niveau' => $niveau]);
    }
}