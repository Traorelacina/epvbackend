<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Liste des articles (admin) avec filtres
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['categorie', 'auteur'])
            ->withTrashed();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->search . '%')
                  ->orWhere('extrait', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($articles);
    }

    /**
     * Détail d'un article (admin)
     */
    public function show(Article $article): JsonResponse
    {
        $article->load(['categorie', 'auteur', 'commentaires' => function ($q) {
            $q->latest()->take(20);
        }]);

        return response()->json(['article' => $article]);
    }

    /**
     * Création d'un article
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug']    = $this->generateSlug($data['titre']);

        // Upload image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        // Date de publication auto si statut publié
        if ($data['statut'] === 'publie' && empty($data['date_publication'])) {
            $data['date_publication'] = now();
        }

        $article = Article::create($data);
        $article->load(['categorie', 'auteur']);

        return response()->json([
            'message' => 'Article créé avec succès.',
            'article' => $article,
        ], 201);
    }

    /**
     * Mise à jour d'un article
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        $data = $request->validated();

        // Nouveau slug si titre changé
        if (isset($data['titre']) && $data['titre'] !== $article->titre) {
            $data['slug'] = $this->generateSlug($data['titre'], $article->id);
        }

        // Upload nouvelle image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        // Date de publication auto
        if (isset($data['statut']) && $data['statut'] === 'publie' && ! $article->date_publication) {
            $data['date_publication'] = now();
        }

        $article->update($data);

        return response()->json([
            'message' => 'Article mis à jour avec succès.',
            'article' => $article->fresh(['categorie', 'auteur']),
        ]);
    }

    /**
     * Suppression d'un article (soft delete)
     */
    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json(['message' => 'Article supprimé avec succès.']);
    }

    /**
     * Restaurer un article supprimé
     */
    public function restore(int $id): JsonResponse
    {
        $article = Article::withTrashed()->findOrFail($id);
        $article->restore();

        return response()->json(['message' => 'Article restauré.', 'article' => $article]);
    }

    /**
     * Supprimer définitivement
     */
    public function forceDelete(int $id): JsonResponse
    {
        $article = Article::withTrashed()->findOrFail($id);

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->forceDelete();

        return response()->json(['message' => 'Article supprimé définitivement.']);
    }

    /**
     * Changer le statut d'un article (publier / dépublier)
     */
    public function toggleStatut(Request $request, Article $article): JsonResponse
    {
        $request->validate([
            'statut' => 'required|in:brouillon,publie,archive',
        ]);

        $data = ['statut' => $request->statut];

        if ($request->statut === 'publie' && ! $article->date_publication) {
            $data['date_publication'] = now();
        }

        $article->update($data);

        return response()->json([
            'message' => 'Statut mis à jour.',
            'article' => $article->fresh(),
        ]);
    }

    // ─── Helper slug ─────────────────────────────────────────────

    private function generateSlug(string $titre, ?int $excludeId = null): string
    {
        $slug  = Str::slug($titre);
        $base  = $slug;
        $count = 1;

        while (true) {
            $query = Article::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (! $query->exists()) {
                break;
            }
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}