<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status'
    ];

    // Append the cover_image attribute to the model
    protected $appends = ['cover_image'];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    // Define the accessor for cover_image
    public function getCoverImageAttribute()
    {
        $firstImage = $this->images()->first();
        return $firstImage ? $firstImage->image : null;
    }

    public function distributorProductQtys()
    {
        return $this->hasMany(DistributorProductQty::class);
    }
}
