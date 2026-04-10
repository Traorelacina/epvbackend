<?php
// app/Http/Controllers/Api/Public/HomepageController.php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ChiffreCle;
use App\Models\Temoignage;
use App\Models\Galerie;
use App\Models\Equipe;
use App\Models\Calendrier;
use Illuminate\Http\JsonResponse;

class HomepageController extends Controller
{
    /**
     * Récupérer toutes les données pour la page d'accueil
     */
    public function index(): JsonResponse
    {
        try {
            // Derniers articles (3 derniers articles publiés)
            $articles = Article::where('publiee', true)
                ->with('categorie')
                ->latest()
                ->take(3)
                ->get();

            // Chiffres clés actifs
            $chiffresCles = ChiffreCle::where('actif', true)
                ->orderBy('ordre')
                ->get();

            // Témoignages validés
            $temoignages = Temoignage::where('valide', true)
                ->orderBy('ordre')
                ->take(6)
                ->get();

            // Dernières galeries (4 dernières galeries publiées)
            $galeries = Galerie::where('publiee', true)
                ->latest()
                ->take(4)
                ->get();

            // Membres de l'équipe affichés sur la homepage
            $equipe = Equipe::where('affiche', true)
                ->orderBy('ordre')
                ->take(4)
                ->get();

            // Prochains événements du calendrier
            $evenements = Calendrier::where('actif', true)
                ->where('date_debut', '>=', now())
                ->orderBy('date_debut')
                ->take(5)
                ->get();

            return response()->json([
                'articles' => $articles,
                'chiffres_cles' => $chiffresCles,
                'temoignages' => $temoignages,
                'galeries' => $galeries,
                'equipe' => $equipe,
                'evenements' => $evenements,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('HomepageController error: ' . $e->getMessage());
            
            // Retourner une réponse partielle même en cas d'erreur
            return response()->json([
                'articles' => [],
                'chiffres_cles' => [],
                'temoignages' => [],
                'galeries' => [],
                'equipe' => [],
                'evenements' => [],
                'error' => $e->getMessage()
            ], 200); // On retourne 200 avec des données vides plutôt qu'une erreur
        }
    }
}