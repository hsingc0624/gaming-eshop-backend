<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $price_cents
 * @property int|null $sale_price_cents
 * @property bool $is_active
 * @property-read int $display_price_cents
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductImage[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductVariant[] $variants
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','description','price_cents','sale_price_cents','is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function getDisplayPriceCentsAttribute(): int
    {
        return $this->sale_price_cents ?? $this->price_cents;
    }
}
