<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $table = 'categories';
    
    protected $fillable = [
        'nom',
        'couleur',
        'description',
        'slug'
    ];

    /**
     * Relation avec les articles
     * UNE catégorie a PLUSIEURS articles
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'categorie_id');
    }

    /**
     * Alternative : alias pour la relation
     */
    public function articlesRelation()
    {
        return $this->hasMany(Article::class, 'categorie_id');
    }
}