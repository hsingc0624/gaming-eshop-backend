<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $product_id
 * @property string $sku
 * @property array|null $options
 * @property int $price_cents
 * @property int $stock
 *
 * @property-read \App\Models\Product $product
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','sku','options','price_cents','stock'];

    protected $casts = [
        'options' => 'array', 
        'price_cents' => 'integer',
        'stock' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
