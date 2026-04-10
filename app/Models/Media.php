<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'medias_galerie';

    protected $fillable = [
        'galerie_id',
        'type',       // 'photo' | 'video'
        'fichier',    // chemin relatif stocké par Storage
        'url_youtube',
        'legende',
        'ordre',
    ];

    protected $casts = [
        'ordre' => 'integer',
    ];

    // Accesseur pour l'URL complète
    protected $appends = ['url_complete', 'url'];

    public function getUrlCompleteAttribute(): string
    {
        if (!$this->fichier) return '';

        if (filter_var($this->fichier, FILTER_VALIDATE_URL)) {
            return $this->fichier;
        }

        return Storage::disk('public')->url($this->fichier);
    }

    // Compatibilité avec le frontend
    public function getUrlAttribute(): ?string
    {
        return $this->fichier;
    }

    public function galerie(): BelongsTo
    {
        return $this->belongsTo(Galerie::class);
    }
}