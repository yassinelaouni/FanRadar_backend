<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MODÈLE RATING (ÉVALUATIONS)
 * 
 * Représente les évaluations/notes données par les utilisateurs
 */
class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'comments',   // Nombre de commentaires
        'stars',      // Nombre d'étoiles (1-5)
        'user_id',    // Utilisateur qui note
        'content_id', // Contenu noté
    ];

    /**
     * VALIDATION : Étoiles entre 1 et 5
     */
    protected $casts = [
        'stars' => 'integer',
        'comments' => 'integer',
    ];

    /**
     * RELATION MANY-TO-ONE : Une évaluation appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATION MANY-TO-ONE : Une évaluation appartient à un contenu
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
