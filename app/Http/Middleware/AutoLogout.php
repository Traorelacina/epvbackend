<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    /**
     * Déconnecter automatiquement un token inactif depuis plus de 2h.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $token = $user->currentAccessToken();

            // Vérifier si le token a expiré (last_used_at + 2h)
            if ($token && $token->last_used_at) {
                $inactiveMinutes = now()->diffInMinutes($token->last_used_at);

                if ($inactiveMinutes > 120) {
                    $token->delete();
                    return response()->json([
                        'message' => 'Session expirée pour cause d\'inactivité. Veuillez vous reconnecter.',
                        'code'    => 'SESSION_EXPIRED',
                    ], 401);
                }
            }
        }

        return $next($request);
    }
}