<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MODÈLE FAVORITE (FAVORIS)
 * 
 * Table pivot entre utilisateurs et contenus favoris
 */
class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',    // Utilisateur qui aime
        'content_id', // Contenu aimé
    ];

    /**
     * RELATION MANY-TO-ONE : Un favori appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATION MANY-TO-ONE : Un favori appartient à un contenu
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
