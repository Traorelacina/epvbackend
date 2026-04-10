<?php

use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CalendrierController as AdminCalendrierController;
use App\Http\Controllers\Api\Admin\CategorieController as AdminCategorieController;
use App\Http\Controllers\Api\Admin\ChiffreCleController as AdminChiffreCleController;
use App\Http\Controllers\Api\Admin\CommentaireController as AdminCommentaireController;
use App\Http\Controllers\Api\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\EquipeController as AdminEquipeController;
use App\Http\Controllers\Api\Admin\GalerieController as AdminGalerieController;
use App\Http\Controllers\Api\Admin\HoraireController as AdminHoraireController;
use App\Http\Controllers\Api\Admin\NiveauController as AdminNiveauController;
use App\Http\Controllers\Api\Admin\PageController as AdminPageController;
use App\Http\Controllers\Api\Admin\TemoignageController as AdminTemoignageController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Public\ArticleController as PublicArticleController;
use App\Http\Controllers\Api\Public\ContactController as PublicContactController;
use App\Http\Controllers\Api\Public\GalerieController as PublicGalerieController;
use App\Http\Controllers\Api\Public\HomepageController;
use App\Http\Controllers\Api\Public\InfosController;
use App\Http\Controllers\Api\Public\NiveauController as PublicNiveauController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques (frontend React)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Homepage ──────────────────────────────────────────────────
    Route::get('/homepage', [HomepageController::class, 'index']);

    // ── Articles / Blog ───────────────────────────────────────────
    Route::get('/articles', [PublicArticleController::class, 'index']);
    Route::get('/articles/{slug}', [PublicArticleController::class, 'show']);
    Route::post('/articles/{slug}/commentaires', [PublicArticleController::class, 'storeCommentaire'])
        ->middleware('throttle:5,1'); // max 5 commentaires par minute par IP

    // ── Galeries ──────────────────────────────────────────────────
    Route::get('/galeries', [PublicGalerieController::class, 'index']);
    Route::get('/galeries/{slug}', [PublicGalerieController::class, 'show']);

    // ── Niveaux scolaires ─────────────────────────────────────────
    Route::get('/niveaux', [PublicNiveauController::class, 'index']);
    Route::get('/niveaux/{slug}', [PublicNiveauController::class, 'show']);

    // ── Informations pratiques ────────────────────────────────────
    Route::get('/horaires', [InfosController::class, 'horaires']);
    Route::get('/calendrier', [InfosController::class, 'calendrier']);
    Route::get('/equipe', [InfosController::class, 'equipe']);
    Route::get('/pages/{slug}', [InfosController::class, 'page']);

    // ── Contact ───────────────────────────────────────────────────
    Route::post('/contact', [PublicContactController::class, 'store'])
        ->middleware('throttle:3,1'); // max 3 envois par minute par IP

    /*
    |--------------------------------------------------------------------------
    | Routes administration (back-office)
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->group(function () {

        // ── Authentification (non protégées) ──────────────────────
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:10,1');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->middleware('throttle:5,1');
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);

        // ── Routes protégées par Sanctum ──────────────────────────
        Route::middleware(['auth:sanctum', 'auto_logout'])->group(function () {

            // Auth
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/me/password', [AuthController::class, 'updatePassword']);

            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index']);

            // ── Gestion utilisateurs (super_admin seulement) ──────
            Route::middleware('role:super_admin')->group(function () {
                Route::get('/users', [UserController::class, 'index']);
                Route::post('/users', [UserController::class, 'store']);
                Route::get('/users/{user}', [UserController::class, 'show']);
                Route::put('/users/{user}', [UserController::class, 'update']);
                Route::delete('/users/{user}', [UserController::class, 'destroy']);
                Route::post('/users/{id}/restore', [UserController::class, 'restore']);
            });

            // ── Articles ──────────────────────────────────────────
            Route::get('/articles', [AdminArticleController::class, 'index']);
            Route::post('/articles', [AdminArticleController::class, 'store']);
            Route::get('/articles/{article}', [AdminArticleController::class, 'show']);
            Route::post('/articles/{article}', [AdminArticleController::class, 'update']); // POST pour multipart
            Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy']);
            Route::post('/articles/{article}/statut', [AdminArticleController::class, 'toggleStatut']);
            Route::post('/articles/{id}/restore', [AdminArticleController::class, 'restore']);
            Route::delete('/articles/{id}/force', [AdminArticleController::class, 'forceDelete'])
                ->middleware('role:super_admin,admin');

            // ── Commentaires ──────────────────────────────────────
            Route::get('/commentaires', [AdminCommentaireController::class, 'index']);
            Route::post('/commentaires/{commentaire}/approve', [AdminCommentaireController::class, 'approve']);
            Route::post('/commentaires/{commentaire}/reject', [AdminCommentaireController::class, 'reject']);
            Route::delete('/commentaires/{commentaire}', [AdminCommentaireController::class, 'destroy']);
            Route::post('/commentaires/bulk-approve', [AdminCommentaireController::class, 'bulkApprove']);
            Route::post('/commentaires/bulk-delete', [AdminCommentaireController::class, 'bulkDelete']);

            // ── Catégories ────────────────────────────────────────
            Route::get('/categories', [AdminCategorieController::class, 'index']);
            Route::post('/categories', [AdminCategorieController::class, 'store']);
            Route::put('/categories/{categorie}', [AdminCategorieController::class, 'update']);
            Route::delete('/categories/{categorie}', [AdminCategorieController::class, 'destroy']);

            // ── Galeries ──────────────────────────────────────────
           // ── Galeries ──────────────────────────────────────────────────────────
Route::get('/galeries', [AdminGalerieController::class, 'index']);
Route::post('/galeries', [AdminGalerieController::class, 'store']);
Route::get('/galeries/{galerie}', [AdminGalerieController::class, 'show']);
Route::post('/galeries/{galerie}', [AdminGalerieController::class, 'update']); // POST pour multipart
Route::delete('/galeries/{galerie}', [AdminGalerieController::class, 'destroy']);
Route::post('/galeries/{galerie}/toggle-publier', [AdminGalerieController::class, 'togglePublier']);
Route::post('/galeries/{galerie}/upload-photo', [AdminGalerieController::class, 'uploadPhoto']);
Route::post('/galeries/{galerie}/reorder-medias', [AdminGalerieController::class, 'reorderMedias']);
Route::delete('/galeries/{galerie}/medias/{media}', [AdminGalerieController::class, 'deleteMedia']); // ← CORRECTION ICI
            

            // ── Messages de contact ───────────────────────────────
            Route::get('/contacts', [AdminContactController::class, 'index']);
            Route::get('/contacts/{contact}', [AdminContactController::class, 'show']);
            Route::post('/contacts/{contact}/toggle-lu', [AdminContactController::class, 'toggleLu']);
            Route::post('/contacts/{contact}/archive', [AdminContactController::class, 'archive']);
            Route::post('/contacts/{contact}/unarchive', [AdminContactController::class, 'unarchive']);
            Route::post('/contacts/{contact}/repondre', [AdminContactController::class, 'repondre']);
            Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy']);
            Route::post('/contacts/bulk-delete', [AdminContactController::class, 'bulkDelete']);

            // ── Niveaux scolaires ─────────────────────────────────
            Route::get('/niveaux', [AdminNiveauController::class, 'index']);
            Route::post('/niveaux', [AdminNiveauController::class, 'store']);
            Route::get('/niveaux/{niveau}', [AdminNiveauController::class, 'show']);
            Route::put('/niveaux/{niveau}', [AdminNiveauController::class, 'update']);
            Route::delete('/niveaux/{niveau}', [AdminNiveauController::class, 'destroy']);
            Route::post('/niveaux/reorder', [AdminNiveauController::class, 'reorder']);
            // Frais de scolarité
            Route::post('/niveaux/{niveau}/frais', [AdminNiveauController::class, 'addFrais']);
            Route::put('/niveaux/{niveau}/frais/{fraisId}', [AdminNiveauController::class, 'updateFrais']);
            Route::delete('/niveaux/{niveau}/frais/{fraisId}', [AdminNiveauController::class, 'deleteFrais']);

            // ── Témoignages ───────────────────────────────────────
            Route::get('/temoignages', [AdminTemoignageController::class, 'index']);
            Route::post('/temoignages', [AdminTemoignageController::class, 'store']);
            Route::get('/temoignages/{temoignage}', [AdminTemoignageController::class, 'show']);
            Route::post('/temoignages/{temoignage}', [AdminTemoignageController::class, 'update']); // POST pour multipart
            Route::delete('/temoignages/{temoignage}', [AdminTemoignageController::class, 'destroy']);
            Route::post('/temoignages/{temoignage}/valider', [AdminTemoignageController::class, 'valider']);
            Route::post('/temoignages/{temoignage}/rejeter', [AdminTemoignageController::class, 'rejeter']);
            Route::post('/temoignages/reorder', [AdminTemoignageController::class, 'reorder']);

            // ── Équipe pédagogique ────────────────────────────────
            Route::get('/equipe', [AdminEquipeController::class, 'index']);
            Route::post('/equipe', [AdminEquipeController::class, 'store']);
            Route::get('/equipe/{equipe}', [AdminEquipeController::class, 'show']);
            Route::post('/equipe/{equipe}', [AdminEquipeController::class, 'update']); // POST pour multipart
            Route::delete('/equipe/{equipe}', [AdminEquipeController::class, 'destroy']);
            Route::post('/equipe/{equipe}/toggle-affiche', [AdminEquipeController::class, 'toggleAffiche']);
            Route::post('/equipe/reorder', [AdminEquipeController::class, 'reorder']);

            // ── Chiffres clés ─────────────────────────────────────
            Route::get('/chiffres-cles', [AdminChiffreCleController::class, 'index']);
            Route::post('/chiffres-cles', [AdminChiffreCleController::class, 'store']);
            Route::put('/chiffres-cles/{chiffreCle}', [AdminChiffreCleController::class, 'update']);
            Route::delete('/chiffres-cles/{chiffreCle}', [AdminChiffreCleController::class, 'destroy']);
            Route::post('/chiffres-cles/{chiffreCle}/toggle-actif', [AdminChiffreCleController::class, 'toggleActif']);
            Route::post('/chiffres-cles/reorder', [AdminChiffreCleController::class, 'reorder']);

            // ── Calendrier scolaire ───────────────────────────────
            Route::get('/calendrier', [AdminCalendrierController::class, 'index']);
            Route::post('/calendrier', [AdminCalendrierController::class, 'store']);
            Route::get('/calendrier/{calendrier}', [AdminCalendrierController::class, 'show']);
            Route::put('/calendrier/{calendrier}', [AdminCalendrierController::class, 'update']);
            Route::delete('/calendrier/{calendrier}', [AdminCalendrierController::class, 'destroy']);
            Route::post('/calendrier/{calendrier}/toggle-actif', [AdminCalendrierController::class, 'toggleActif']);

            // ── Horaires ──────────────────────────────────────────
            Route::get('/horaires', [AdminHoraireController::class, 'index']);
            Route::post('/horaires', [AdminHoraireController::class, 'store']);
            Route::put('/horaires/{horaire}', [AdminHoraireController::class, 'update']);
            Route::delete('/horaires/{horaire}', [AdminHoraireController::class, 'destroy']);
            Route::post('/horaires/reorder', [AdminHoraireController::class, 'reorder']);

            // ── Pages statiques ───────────────────────────────────
            Route::get('/pages', [AdminPageController::class, 'index']);
            Route::get('/pages/{slug}', [AdminPageController::class, 'show']);
            Route::put('/pages/{slug}', [AdminPageController::class, 'update']);
        });
    });
});