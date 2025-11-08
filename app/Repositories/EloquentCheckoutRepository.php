<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentCheckoutRepository implements CheckoutRepositoryInterface
{
    /**
     * @param string $token
     * @return Cart|null
     */
    public function findCartByToken(string $token): ?Cart
    {
        return Cart::where('cart_token', $token)
            ->with(['items.product', 'items.variant'])
            ->first();
    }

    /**
     * @param array $data
     * @return Order
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'number'          => Str::upper(Str::random(10)),
                'user_id'         => auth()->id(),
                'status'          => 'pending',
                'subtotal_cents'  => $data['subtotal'],
                'discount_cents'  => $data['discount'],
                'shipping_cents'  => $data['shipping_cents'],
                'tax_cents'       => $data['tax'],
                'total_cents'     => $data['total'],
                'payment_method'  => $data['payment_method'] ?? null,
                'payment_ref'     => null,
            ]);

            foreach ($data['cart']->items as $ci) {
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

            $order->addresses()->create(array_merge($data['shipping_address'], ['type' => 'shipping']));
            $order->addresses()->create(array_merge($data['billing_address'],  ['type' => 'billing']));

            $data['cart']->items()->delete();

            return $order->load(['items', 'addresses']);
        });
    }
}
