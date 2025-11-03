<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function checkout(Request $r)
    {
        $data = $r->validate([
            'cart_token'    => ['required','uuid'],
            'shipping'      => ['required','array'],
            'billing'       => ['required','array'],
            'payment_method'=> ['required','string'],
            'coupon'        => ['nullable','string'],
        ]);

        $cart = Cart::where('cart_token', $data['cart_token'])
            ->with(['items.product','items.variant'])
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        $subtotal = $cart->items->sum(fn($i) => $i->price_cents * $i->qty);

        $discount = 0;
        if (!empty($data['coupon'])) {
            $c = Coupon::where('code', $data['coupon'])->first();
            if ($c && $c->isActive()) {
                $discount = $c->type === 'percent'
                    ? (int) round($subtotal * $c->value / 100)
                    : (int) $c->value;
                $c->increment('uses');
            }
        }

        $shipping = 0;
        $tax = (int) round($subtotal * 0.2);
        $total = max(0, $subtotal - $discount + $shipping + $tax);

        $order = DB::transaction(function () use ($cart, $data, $subtotal, $discount, $shipping, $tax, $total) {
            $order = Order::create([
                'number'          => Str::upper(Str::random(10)),
                'user_id'         => auth()->id(),
                'status'          => 'pending', 
                'subtotal_cents'  => $subtotal,
                'discount_cents'  => $discount,
                'shipping_cents'  => $shipping,
                'tax_cents'       => $tax,
                'total_cents'     => $total,
                'payment_method'  => $data['payment_method'],
                'payment_ref'     => (string) Str::uuid(),
            ]);

            foreach ($cart->items as $ci) {
                $order->items()->create([
                    'product_id'         => $ci->product_id,
                    'product_variant_id' => $ci->product_variant_id,
                    'name'               => $ci->product?->name ?? 'Item',
                    'sku'                => $ci->variant?->sku ?? null,
                    'price_cents'        => $ci->price_cents,
                    'qty'                => $ci->qty,
                    'subtotal_cents'     => $ci->price_cents * $ci->qty,
                ]);
            }

            $order->addresses()->create(array_merge($data['shipping'], ['type' => 'shipping']));
            $order->addresses()->create(array_merge($data['billing'],  ['type' => 'billing']));

            $cart->items()->delete();

            return $order->load(['items','addresses']);
        });

        return response()->json($order);
    }
}
