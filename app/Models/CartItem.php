<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property int $price_cents
 * @property int $qty
 * @property-read int $subtotal_cents
 *
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductVariant|null $variant
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id','product_id','product_variant_id','price_cents','qty'
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'qty'         => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getSubtotalCentsAttribute(): int
    {
        return $this->price_cents * $this->qty;
    }
}
