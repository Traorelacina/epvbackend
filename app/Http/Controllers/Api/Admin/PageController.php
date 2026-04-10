<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Liste des pages statiques
     */
    public function index(): JsonResponse
    {
        $pages = Page::all(['id', 'slug', 'titre', 'meta_titre', 'updated_at']);

        return response()->json(['pages' => $pages]);
    }

    /**
     * Contenu d'une page par son slug
     */
    public function show(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return response()->json(['page' => $page]);
    }

    /**
     * Mettre à jour le contenu d'une page
     */
    public function update(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'titre'            => 'sometimes|string|max:255',
            'contenu'          => 'nullable',
            'meta_titre'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $page = Page::where('slug', $slug)->firstOrFail();

        $data = $request->only('titre', 'contenu', 'meta_titre', 'meta_description');

        // Si le contenu est un tableau/objet, on le sérialise en JSON
        if (isset($data['contenu']) && is_array($data['contenu'])) {
            $data['contenu'] = json_encode($data['contenu'], JSON_UNESCAPED_UNICODE);
        }

        $page->update($data);

        return response()->json([
            'message' => 'Page mise à jour avec succès.',
            'page'    => $page->fresh(),
        ]);
    }
}