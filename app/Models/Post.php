<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
        'user_id',
        'feedback',
        'schedule_at',
        'description',
        'content_status',

        'subcategory_id',

    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation polymorphe vers medias (si tu veux gérer ça)
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Relation avec les favoris
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    // Utilisateurs qui ont mis ce post en favori
    public function favoritedBy()
    {
        return $this->morphToMany(User::class, 'favoriteable', 'favorites');
    }
}
