<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Commentaire;
use App\Models\Contact;
use App\Models\Galerie;
use App\Models\Temoignage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Statistiques globales du tableau de bord
     */
    public function index(Request $request): JsonResponse
    {
        // Compteurs principaux
        $stats = [
            'messages_non_lus'      => Contact::where('lu', false)->where('archive', false)->count(),
            'articles_publies'      => Article::where('statut', 'publie')->count(),
            'articles_brouillons'   => Article::where('statut', 'brouillon')->count(),
            'commentaires_attente'  => Commentaire::where('approuve', false)->count(),
            'temoignages_attente'   => Temoignage::where('valide', false)->count(),
            'galeries_publiees'     => Galerie::where('publiee', true)->count(),
        ];

        // Articles récents
        $articles_recents = Article::with('categorie', 'auteur')
            ->latest()
            ->take(5)
            ->get(['id', 'titre', 'statut', 'vues', 'created_at', 'user_id', 'categorie_id']);

        // Messages récents non lus
        $messages_recents = Contact::where('lu', false)
            ->where('archive', false)
            ->latest()
            ->take(5)
            ->get(['id', 'nom', 'email', 'sujet', 'created_at']);

        return response()->json([
            'stats'            => $stats,
            'articles_recents' => $articles_recents,
            'messages_recents' => $messages_recents,
        ]);
    }
}