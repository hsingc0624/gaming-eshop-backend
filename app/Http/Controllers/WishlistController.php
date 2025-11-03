<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $w = Wishlist::firstOrCreate(['user_id' => auth()->id()]);
        return response()->json(
            $w->items()->with('product.images')->get()
        );
    }

    public function toggle(Product $product)
    {
        $w = Wishlist::firstOrCreate(['user_id' => auth()->id()]);
        $exists = $w->items()->where('product_id', $product->id)->exists();

        if ($exists) {
            $w->items()->where('product_id', $product->id)->delete();
        } else {
            $w->items()->create(['product_id' => $product->id]);
        }

        return response()->json(['added' => ! $exists]);
    }
}
