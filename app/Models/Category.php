<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }
}
