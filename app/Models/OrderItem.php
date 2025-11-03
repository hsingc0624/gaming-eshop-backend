<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
