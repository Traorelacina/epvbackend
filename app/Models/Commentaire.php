<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'nom',
        'email',
        'contenu',
        'approuve',
        'ip_address',
    ];

    protected $casts = [
        'approuve' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation inverse avec l'article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
