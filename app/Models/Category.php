<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // Une catégorie a plusieurs sous-catégories
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    
    public function userPreferredCategories()
    {
        return $this->hasMany(UserPreferredCategory::class);
    }
}
