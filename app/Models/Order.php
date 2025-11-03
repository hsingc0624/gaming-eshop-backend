<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $number
 * @property int|null $user_id
 * @property string $status
 * @property int $subtotal_cents
 * @property int $discount_cents
 * @property int $shipping_cents
 * @property int $tax_cents
 * @property int $total_cents
 * @property string $payment_method
 * @property string $payment_ref
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Address[] $addresses
 * @property-read \App\Models\User|null $user
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number','user_id','status',
        'subtotal_cents','discount_cents','shipping_cents','tax_cents','total_cents',
        'payment_method','payment_ref'
    ];

    protected $casts = [
        'subtotal_cents' => 'integer',
        'discount_cents' => 'integer',
        'shipping_cents' => 'integer',
        'tax_cents'      => 'integer',
        'total_cents'    => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
