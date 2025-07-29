<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MODÈLE COMMUNITY (COMMUNAUTÉS)
 * 
 * Représente les communautés organisées par sous-catégories
 */
class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',           // Nom de la communauté
        'description',    // Description de la communauté
        'subcategory_id', // Sous-catégorie associée
    ];

    /**
     * RELATION MANY-TO-ONE : Une communauté appartient à une sous-catégorie
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * RELATION MANY-TO-MANY : Une communauté peut avoir plusieurs membres
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'community_user');
    }
}
