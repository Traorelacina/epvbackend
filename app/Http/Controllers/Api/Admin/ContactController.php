<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReponseContactMail;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Liste des messages de contact
     */
    public function index(Request $request): JsonResponse
    {
        $query = Contact::query();

        if ($request->filled('lu')) {
            $query->where('lu', filter_var($request->lu, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('archive')) {
            $query->where('archive', filter_var($request->archive, FILTER_VALIDATE_BOOLEAN));
        } else {
            $query->where('archive', false);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('sujet', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $contacts = $query->latest()->paginate($request->integer('per_page', 20));

        // Compteurs
        $counts = [
            'total'     => Contact::where('archive', false)->count(),
            'non_lus'   => Contact::where('lu', false)->where('archive', false)->count(),
            'archives'  => Contact::where('archive', true)->count(),
        ];

        return response()->json([
            'contacts' => $contacts,
            'counts'   => $counts,
        ]);
    }

    /**
     * Détail d'un message
     */
    public function show(Request $request, Contact $contact): JsonResponse
    {
        // Marquer comme lu automatiquement à la lecture
        if (! $contact->lu) {
            $contact->update([
                'lu'     => true,
                'lu_at'  => now(),
                'lu_par' => $request->user()->id,
            ]);
        }

        return response()->json(['contact' => $contact]);
    }

    /**
     * Marquer comme lu/non lu
     */
    public function toggleLu(Request $request, Contact $contact): JsonResponse
    {
        $contact->update([
            'lu'     => ! $contact->lu,
            'lu_at'  => ! $contact->lu ? now() : null,
            'lu_par' => ! $contact->lu ? $request->user()->id : null,
        ]);

        return response()->json([
            'message' => $contact->lu ? 'Marqué comme lu.' : 'Marqué comme non lu.',
            'contact' => $contact->fresh(),
        ]);
    }

    /**
     * Archiver un message
     */
    public function archive(Contact $contact): JsonResponse
    {
        $contact->update(['archive' => true]);

        return response()->json(['message' => 'Message archivé.']);
    }

    /**
     * Désarchiver un message
     */
    public function unarchive(Contact $contact): JsonResponse
    {
        $contact->update(['archive' => false]);

        return response()->json(['message' => 'Message désarchivé.']);
    }

    /**
     * Répondre à un message par email
     */
    public function repondre(Request $request, Contact $contact): JsonResponse
    {
        $request->validate([
            'reponse' => 'required|string|min:10',
        ]);

        // Envoyer l'email de réponse
        Mail::to($contact->email)->send(new ReponseContactMail($contact, $request->reponse));

        $contact->update([
            'reponse'     => $request->reponse,
            'repondu_at'  => now(),
            'repondu_par' => $request->user()->id,
            'lu'          => true,
            'lu_at'       => $contact->lu_at ?? now(),
            'lu_par'      => $contact->lu_par ?? $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Réponse envoyée avec succès.',
            'contact' => $contact->fresh(),
        ]);
    }

    /**
     * Supprimer un message
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(['message' => 'Message supprimé.']);
    }

    /**
     * Suppression en masse
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:contacts,id']);

        Contact::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => count($request->ids) . ' message(s) supprimé(s).']);
    }
}