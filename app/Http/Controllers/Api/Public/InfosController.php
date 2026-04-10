<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Calendrier;
use App\Models\Equipe;
use App\Models\Horaire;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InfosController extends Controller
{
    /**
     * Horaires scolaires
     */
    public function horaires(): JsonResponse
    {
        $horaires = Horaire::orderBy('ordre')->get();

        return response()->json(['horaires' => $horaires]);
    }

    /**
     * Calendrier scolaire (événements actifs à venir ou de l'année en cours)
     */
    public function calendrier(Request $request): JsonResponse
    {
        $query = Calendrier::where('actif', true);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('annee')) {
            $query->whereYear('date_debut', $request->annee);
        } else {
            // Par défaut : événements de l'année scolaire en cours
            $query->whereYear('date_debut', now()->year);
        }

        $calendrier = $query->orderBy('date_debut')->get();

        return response()->json(['calendrier' => $calendrier]);
    }

    /**
     * Équipe pédagogique visible
     */
    public function equipe(): JsonResponse
    {
        $equipe = Equipe::where('affiche', true)
            ->orderBy('ordre')
            ->get();

        return response()->json(['equipe' => $equipe]);
    }

    /**
     * Contenu d'une page statique
     */
    public function page(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        // Décoder le contenu JSON si applicable
        $contenu = $page->contenu;
        if (is_string($contenu)) {
            $decoded = json_decode($contenu, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $contenu = $decoded;
            }
        }

        return response()->json([
            'page' => [
                'slug'             => $page->slug,
                'titre'            => $page->titre,
                'contenu'          => $contenu,
                'meta_titre'       => $page->meta_titre,
                'meta_description' => $page->meta_description,
            ],
        ]);
    }
}