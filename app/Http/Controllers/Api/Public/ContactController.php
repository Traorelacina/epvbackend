<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactRequest;
use App\Mail\NouveauContactMail;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Soumettre un message de contact
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create([
            'nom'        => $request->nom,
            'email'      => $request->email,
            'telephone'  => $request->telephone,
            'sujet'      => $request->sujet,
            'message'    => $request->message,
            'ip_address' => $request->ip(),
        ]);

        // Notifier l'administration par email
        try {
            Mail::to(config('mail.admin_address', 'admin@epvmarel.ci'))
                ->send(new NouveauContactMail($contact));
        } catch (\Exception $e) {
            // Ne pas bloquer la réponse si l'email échoue
            \Log::error('Erreur envoi email contact: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
        ], 201);
    }
}