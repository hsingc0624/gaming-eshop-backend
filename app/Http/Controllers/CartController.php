<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function attach(Request $r)
    {
        $token = $r->input('cart_token') ?: (string) Str::uuid();

        $cart = Cart::firstOrCreate(
            ['cart_token' => $token],
            ['user_id' => auth()->id()]
        );

        if (auth()->check() && ! $cart->user_id) {
            $cart->update(['user_id' => auth()->id()]);
        }

        return response()->json([
            'cart_token' => $token,
            'cart' => $cart->load(['items.product','items.variant']),
        ]);
    }

    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function add(Request $r)
    {
        $data = $r->validate([
            'cart_token'         => ['required','uuid'],
            'product_id'         => ['required','exists:products,id'],
            'product_variant_id' => ['nullable','exists:product_variants,id'],
            'qty'                => ['required','integer','min:1'],
        ]);

        $cart = Cart::where('cart_token', $data['cart_token'])->firstOrFail();
        $product = Product::findOrFail($data['product_id']);

        $price = $product->sale_price_cents ?? $product->price_cents;
        if (!empty($data['product_variant_id'])) {
            $variant = ProductVariant::findOrFail($data['product_variant_id']);
            $price = $variant->price_cents;
        }

        $item = $cart->items()
            ->where('product_id', $data['product_id'])
            ->where('product_variant_id', $data['product_variant_id'] ?? null)
            ->first();

        if ($item) {
            $item->increment('qty', (int)$data['qty']);
        } else {
            $item = $cart->items()->create([
                'product_id'         => $data['product_id'],
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'price_cents'        => $price,
                'qty'                => (int)$data['qty'],
            ]);
        }

        return response()->json($item->fresh()->load(['product','variant']));
    }

    /**
     * @param  Request  $r
     * @param  int      $id
     * @return JsonResponse
     */
    public function update(Request $r, int $id)
    {
        $data = $r->validate([
            'qty' => ['required','integer','min:1']
        ]);

        $item = CartItem::findOrFail($id);
        $item->update(['qty' => (int)$data['qty']]);

        return response()->json($item->fresh()->load(['product','variant']));
    }

    /**
     * @param  int  $id
     * @return Response
     */
    public function remove(int $id)
    {
        $item = CartItem::findOrFail($id);
        $item->delete();
        return response()->noContent();
    }

    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function show(Request $r)
    {
        $token = $r->query('cart_token');
        if (! $token) {
            return response()->json(['items' => []]);
        }

        $cart = Cart::where('cart_token', $token)
            ->with(['items.product','items.variant'])
            ->first();

        return response()->json($cart ?: ['items' => []]);
    }
}
