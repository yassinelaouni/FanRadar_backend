<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MODÈLE TAGGABLE (TABLE PIVOT POLYMORPHE)
 * 
 * Gère les relations entre tags et différents modèles
 */
class Taggable extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_id',        // ID du tag
        'taggable_id',   // ID de l'entité taggée
        'taggable_type', // Type de l'entité taggée (Content, Product, etc.)
    ];

    /**
     * RELATION MANY-TO-ONE : Appartient à un tag
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * RELATION POLYMORPHE : Appartient à différents modèles
     */
    public function taggable()
    {
        return $this->morphTo();
    }
}
