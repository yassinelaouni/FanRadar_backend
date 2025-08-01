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
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation polymorphe vers medias (si tu veux gérer ça)
    public function medias()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
public function subcategory()
{
    return $this->belongsTo(Subcategory::class);
}


}
