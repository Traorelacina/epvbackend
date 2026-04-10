<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     */
    protected $table = 'articles';

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
        'titre',
        'slug',
        'extrait',
        'contenu',
        'image',
        'image_alt',
        'statut',
        'date_publication',
        'user_id',
        'categorie_id',
        'meta_titre',
        'meta_description',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'date_publication' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les attributs par défaut.
     */
    protected $attributes = [
        'statut' => 'brouillon',
    ];

    // ==================== RELATIONS ====================

    /**
     * Relation avec l'auteur (utilisateur)
     */
    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias pour auteur
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    /**
     * Relation avec la catégorie
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour inclure les articles supprimés (soft delete)
     */
    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    // ==================== MÉTHODES ====================

    /**
     * Générer un slug unique
     */
    public static function generateUniqueSlug($titre, $excludeId = null)
    {
        $slug = Str::slug($titre);
        $originalSlug = $slug;
        $count = 1;

        while (self::where('slug', $slug)
            ->when($excludeId, function ($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}