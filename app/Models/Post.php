<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [

        'content',
        'user_id',
        'feedback',
        'schedule_at',
        'description',
        'content_status',
        'category_id',
        'subcategory_id',
        'media',
    ];

    protected $casts = [
        'media' => 'array',
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

    // Relation avec les ratings du post
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    // Utilisateurs qui ont noté ce post
    public function ratedBy()
    {
        return $this->morphToMany(User::class, 'rateable', 'ratings');
    }

    // Calculer la note moyenne du post
    public function averageRating()
    {
        return $this->ratings()->avg('evaluation');
    }

    // Compter le nombre total de ratings
    public function ratingsCount()
    {
        return $this->ratings()->count();
    }

    public function medias()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}


