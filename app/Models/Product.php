<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'description',
        'price',
        'stock',
        'promotion',
        'sale_start_date',
        'sale_end_date',
        'user_id',
        'subcategory_id',
        'type',
        'revenue',
        'content_status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_start_date' => 'date',
        'sale_end_date' => 'date',
    ];

    // Relation avec OrderProduct
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    // Relation avec Orders via OrderProduct
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

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
    return $this->belongsTo(SubCategory::class);
}

}
