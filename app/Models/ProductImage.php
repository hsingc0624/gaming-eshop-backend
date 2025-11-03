<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $product_id
 * @property string $url
 * @property int|null $position
 *
 * @property-read \App\Models\Product $product
 */
class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','url','position'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
