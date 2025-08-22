<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'favoriteable_id',
        'favoriteable_type',
    ];

    /**
     * Relation polymorphe vers les éléments qui peuvent être favoris
     * (Post, Product, etc.)
     */
    public function favoriteable()
    {
        return $this->morphTo();
    }

    /**
     * Relation vers l'utilisateur qui a mis en favori
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
