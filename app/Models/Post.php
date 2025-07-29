<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MODÈLE POST (PUBLICATIONS)
 * 
 * Représente les publications/posts créés par les utilisateurs
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',    // Contenu du post
        'user_id', // Auteur du post
    ];

    /**
     * RELATION MANY-TO-ONE : Un post appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATION POLYMORPHE : Un post peut avoir des tags
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
