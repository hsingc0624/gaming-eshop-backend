<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property string $name
 * @property string|null $sku
 * @property int $price_cents
 * @property int $qty
 * @property int $subtotal_cents
 *
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductVariant|null $variant
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','product_id','product_variant_id','name','sku',
        'price_cents','qty','subtotal_cents'
    ];

    protected $casts = [
        'price_cents'   => 'integer',
        'qty'           => 'integer',
        'subtotal_cents'=> 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
