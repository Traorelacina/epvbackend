<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreCommentaireRequest;
use App\Models\Article;
use App\Models\Commentaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Liste des articles publiés (avec pagination et filtres)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with('categorie:id,nom,slug,couleur')
            ->where('statut', 'publie')
            ->where('date_publication', '<=', now());

        if ($request->filled('categorie')) {
            $query->whereHas('categorie', fn ($q) => $q->where('slug', $request->categorie));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->search . '%')
                  ->orWhere('extrait', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query
            ->select('id', 'titre', 'slug', 'extrait', 'image', 'image_alt', 'categorie_id', 'date_publication', 'vues')
            ->orderBy('date_publication', 'desc')
            ->paginate($request->integer('per_page', 9));

        return response()->json($articles);
    }

    /**
     * Détail d'un article publié
     */
    public function show(string $slug): JsonResponse
    {
        $article = Article::with([
                'categorie:id,nom,slug,couleur',
                'commentaires' => fn ($q) => $q->where('approuve', true)->latest(),
            ])
            ->where('slug', $slug)
            ->where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->firstOrFail();

        // Incrémenter les vues
        $article->increment('vues');

        // Articles similaires (même catégorie)
        $similaires = Article::where('categorie_id', $article->categorie_id)
            ->where('id', '!=', $article->id)
            ->where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->select('id', 'titre', 'slug', 'extrait', 'image', 'date_publication')
            ->orderBy('date_publication', 'desc')
            ->take(3)
            ->get();

        return response()->json([
            'article'    => $article,
            'similaires' => $similaires,
        ]);
    }

    /**
     * Soumettre un commentaire sur un article
     */
    public function storeCommentaire(StoreCommentaireRequest $request, string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)
            ->where('statut', 'publie')
            ->firstOrFail();

        $commentaire = $article->commentaires()->create([
            'nom'        => $request->nom,
            'email'      => $request->email,
            'contenu'    => $request->contenu,
            'approuve'   => false,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Votre commentaire a été soumis et sera visible après modération.',
        ], 201);
    }
}