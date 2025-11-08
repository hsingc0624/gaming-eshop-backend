<?php

namespace App\Services;

use App\Repositories\CheckoutRepositoryInterface;
use App\Models\Coupon;

class CheckoutService
{
    /**
     * @param CheckoutRepositoryInterface $repo
     */
    public function __construct(
        private CheckoutRepositoryInterface $repo
    ) {}

    /**
     * @param array $data
     * @return \App\Models\Order
     */
    public function checkout(array $data)
    {
        $cart = $this->repo->findCartByToken($data['cart_token']);

        abort_if(!$cart, 404, 'Cart not found');
        abort_if($cart->items->isEmpty(), 422, 'Cart is empty');

        $subtotal = $cart->items->sum(fn($i) => $i->price_cents * $i->qty);

        $discount = 0;

        if (!empty($data['coupon'])) {
            $coupon = Coupon::where('code', $data['coupon'])->first();

            if ($coupon && $coupon->isActive()) {
                $discount = $coupon->type === 'percent'
                    ? (int) round($subtotal * $coupon->value / 100)
                    : (int) $coupon->value;

                $coupon->increment('uses');
            }
        }

        $shippingCost = 0;
        $tax          = (int) round($subtotal * 0.2);
        $total        = max(0, $subtotal - $discount + $shippingCost + $tax);

        return $this->repo->createOrder([
            'cart'             => $cart,
            'shipping_address' => $data['shipping'], 
            'billing_address'  => $data['billing'], 
            'payment_method'   => $data['payment_method'] ?? null,
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'shipping_cents'   => $shippingCost, 
            'tax'              => $tax,
            'total'            => $total,
        ]);
    }
}
