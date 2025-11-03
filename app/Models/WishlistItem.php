<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WishlistItem extends Model
{
    use HasFactory;

    protected $fillable = ['wishlist_id','product_id'];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
