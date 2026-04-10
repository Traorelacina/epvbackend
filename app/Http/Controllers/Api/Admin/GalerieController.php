<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galerie;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalerieController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Galerie::withCount('medias');

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        if ($request->filled('publiee')) {
            $query->where('publiee', filter_var($request->publiee, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        $galeries = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json($galeries);
    }

    public function show(Galerie $galerie): JsonResponse
    {
        $galerie->load(['medias' => fn($q) => $q->orderBy('ordre')]);

        return response()->json(['galerie' => $galerie]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categorie' => 'required|string',
            'annee' => 'required|integer',
            'publiee' => 'boolean',
            'image_couverture' => 'nullable|image|max:5120'
        ]);

        $data = $request->all();
        $data['slug'] = $this->generateSlug($data['titre']);

        if ($request->hasFile('image_couverture')) {
            $data['image_couverture'] = $request->file('image_couverture')->store('galeries/couvertures', 'public');
        }

        $galerie = Galerie::create($data);

        return response()->json([
            'message' => 'Galerie créée avec succès.',
            'galerie' => $galerie,
        ], 201);
    }

    public function update(Request $request, Galerie $galerie): JsonResponse
    {
        $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'categorie' => 'sometimes|string',
            'annee' => 'sometimes|integer',
            'publiee' => 'boolean',
            'image_couverture' => 'nullable|image|max:5120'
        ]);

        $data = $request->all();

        if (isset($data['titre']) && $data['titre'] !== $galerie->titre) {
            $data['slug'] = $this->generateSlug($data['titre'], $galerie->id);
        }

        if ($request->hasFile('image_couverture')) {
            if ($galerie->image_couverture) {
                Storage::disk('public')->delete($galerie->image_couverture);
            }
            $data['image_couverture'] = $request->file('image_couverture')->store('galeries/couvertures', 'public');
        }

        $galerie->update($data);

        return response()->json([
            'message' => 'Galerie mise à jour.',
            'galerie' => $galerie->fresh()->load('medias'),
        ]);
    }

    public function destroy(Galerie $galerie): JsonResponse
    {
        $galerie->delete();

        return response()->json(['message' => 'Galerie supprimée.']);
    }

    public function uploadPhoto(Request $request, Galerie $galerie): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|max:5120',
            'legende' => 'nullable|string|max:255',
        ]);

        $ordre = ($galerie->medias()->max('ordre') ?? 0) + 1;
        
        $path = $request->file('file')->store('galeries/photos', 'public');

        $media = Media::create([
            'galerie_id' => $galerie->id,
            'type' => 'photo',
            'fichier' => $path,
            'legende' => $request->legende,
            'ordre' => $ordre,
        ]);

        return response()->json([
            'message' => 'Photo ajoutée.',
            'media' => $media->fresh(),
        ], 201);
    }

    /**
     * Supprimer un média - Version avec galerie et media
     * Route: DELETE /admin/galeries/{galerie}/medias/{media}
     */
    public function deleteMedia(Galerie $galerie, Media $media): JsonResponse
    {
        // Vérifier que le média appartient bien à la galerie
        if ($media->galerie_id !== $galerie->id) {
            return response()->json([
                'message' => 'Ce média n\'appartient pas à cette galerie'
            ], 403);
        }

        if ($media->type === 'photo' && $media->fichier) {
            Storage::disk('public')->delete($media->fichier);
        }

        $media->delete();

        return response()->json(['message' => 'Média supprimé.']);
    }

    public function togglePublier(Galerie $galerie): JsonResponse
    {
        $galerie->update(['publiee' => !$galerie->publiee]);

        return response()->json([
            'message' => $galerie->publiee ? 'Galerie publiée.' : 'Galerie dépubliée.',
            'galerie' => $galerie->fresh(),
        ]);
    }

    public function reorderMedias(Request $request, Galerie $galerie): JsonResponse
    {
        $request->validate([
            'medias' => 'required|array',
            'medias.*.id' => 'required|exists:medias_galerie,id',
            'medias.*.ordre' => 'required|integer|min:0'
        ]);
        
        foreach ($request->medias as $item) {
            Media::where('id', $item['id'])
                ->where('galerie_id', $galerie->id)
                ->update(['ordre' => $item['ordre']]);
        }
        
        return response()->json([
            'message' => 'Ordre des médias mis à jour.'
        ]);
    }

    private function generateSlug(string $titre, ?int $excludeId = null): string
    {
        $slug = Str::slug($titre);
        $base = $slug;
        $count = 1;

        while (true) {
            $query = Galerie::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}