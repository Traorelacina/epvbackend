<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Galerie extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titre',
        'slug',
        'description',
        'categorie',
        'annee',
        'image_couverture',
        'publiee',
    ];

    protected $casts = [
        'publiee' => 'boolean',
        'annee'   => 'integer',
    ];

    public function medias(): HasMany
    {
        return $this->hasMany(Media::class, 'galerie_id')->orderBy('ordre');
    }
}