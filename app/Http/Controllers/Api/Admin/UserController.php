<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs administrateurs
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeRole($request, 'super_admin');

        $users = User::withTrashed()
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'is_active', 'last_login_at', 'created_at', 'deleted_at']);

        return response()->json(['users' => $users]);
    }

    /**
     * Détail d'un utilisateur
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $this->authorizeRole($request, 'super_admin');

        $user->load(['loginHistories' => function ($q) {
            $q->latest('logged_at')->take(20);
        }]);

        return response()->json(['user' => $user]);
    }

    /**
     * Création d'un utilisateur admin
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorizeRole($request, 'super_admin');

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Mise à jour d'un utilisateur
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorizeRole($request, 'super_admin');

        // Empêcher de modifier son propre rôle ou de se désactiver
        if ($user->id === $request->user()->id) {
            if ($request->has('role') && $request->role !== $user->role) {
                return response()->json(['message' => 'Vous ne pouvez pas modifier votre propre rôle.'], 403);
            }
            if ($request->has('is_active') && ! $request->boolean('is_active')) {
                return response()->json(['message' => 'Vous ne pouvez pas désactiver votre propre compte.'], 403);
            }
        }

        $data = $request->only(['name', 'email', 'role', 'is_active']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            // Révoquer tous les tokens de cet utilisateur
            $user->tokens()->delete();
        }

        $user->update($data);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'user'    => $user->fresh(),
        ]);
    }

    /**
     * Suppression d'un utilisateur (soft delete)
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeRole($request, 'super_admin');

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }

    /**
     * Restaurer un utilisateur supprimé
     */
    public function restore(Request $request, int $id): JsonResponse
    {
        $this->authorizeRole($request, 'super_admin');

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json(['message' => 'Utilisateur restauré avec succès.', 'user' => $user]);
    }

    // ─── Helper ──────────────────────────────────────────────────

    private function authorizeRole(Request $request, string $role): void
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403, 'Accès réservé au super administrateur.');
        }
    }
}