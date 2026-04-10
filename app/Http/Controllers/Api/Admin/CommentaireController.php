<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commentaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    /**
     * Liste des commentaires avec filtres
     */
    public function index(Request $request): JsonResponse
    {
        $query = Commentaire::with('article:id,titre,slug');

        if ($request->filled('approuve')) {
            $query->where('approuve', filter_var($request->approuve, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('article_id')) {
            $query->where('article_id', $request->article_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('contenu', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $commentaires = $query->latest()->paginate($request->integer('per_page', 20));

        return response()->json($commentaires);
    }

    /**
     * Approuver un commentaire
     */
    public function approve(Commentaire $commentaire): JsonResponse
    {
        $commentaire->update(['approuve' => true]);

        return response()->json([
            'message'      => 'Commentaire approuvé.',
            'commentaire'  => $commentaire->fresh(),
        ]);
    }

    /**
     * Rejeter (désapprouver) un commentaire
     */
    public function reject(Commentaire $commentaire): JsonResponse
    {
        $commentaire->update(['approuve' => false]);

        return response()->json([
            'message'     => 'Commentaire rejeté.',
            'commentaire' => $commentaire->fresh(),
        ]);
    }

    /**
     * Suppression d'un commentaire
     */
    public function destroy(Commentaire $commentaire): JsonResponse
    {
        $commentaire->delete();

        return response()->json(['message' => 'Commentaire supprimé.']);
    }

    /**
     * Approuver plusieurs commentaires en masse
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:commentaires,id']);

        Commentaire::whereIn('id', $request->ids)->update(['approuve' => true]);

        return response()->json(['message' => count($request->ids) . ' commentaire(s) approuvé(s).']);
    }

    /**
     * Supprimer plusieurs commentaires en masse
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:commentaires,id']);

        Commentaire::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => count($request->ids) . ' commentaire(s) supprimé(s).']);
    }
}