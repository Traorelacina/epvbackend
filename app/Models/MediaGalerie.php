<?php
// app/Models/MediaGalerie.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaGalerie extends Model
{
    protected $table = 'medias_galerie';
    
    protected $fillable = [
        'galerie_id',
        'type',
        'fichier',
        'titre',
        'description',
        'ordre'
    ];

    /**
     * Relation inverse avec la galerie
     */
    public function galerie(): BelongsTo
    {
        return $this->belongsTo(Galerie::class);
    }
}