<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    /**
     * Relation vers l'utilisateur qui suit (follower)
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Relation vers l'utilisateur qui est suivi (following)
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    /**
     * Scope pour obtenir les follows d'un utilisateur spécifique
     */
    public function scopeByFollower($query, $userId)
    {
        return $query->where('follower_id', $userId);
    }

    /**
     * Scope pour obtenir les followers d'un utilisateur spécifique
     */
    public function scopeByFollowing($query, $userId)
    {
        return $query->where('following_id', $userId);
    }
}
