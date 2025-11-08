<?php
 
namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;

class EloquentCartRepository implements CartRepositoryInterface
{
    /**
     * @param string $token
     * @return Cart|null
     */
    public function findByToken(string $token): ?Cart
    {
        return Cart::where('cart_token', $token)
            ->with('items')
            ->first();
    }

    /**
     * @param int $userId
     * @return Cart|null
     */
    public function findByUserId(int $userId): ?Cart
    {
        return Cart::where('user_id', $userId)
            ->with('items')
            ->first();
    }

    /**
     * @param array $attrs
     * @return Cart
     */
    public function create(array $attrs): Cart
    {
        return Cart::create($attrs);
    }

    /**
     * @param Cart $cart
     * @return Cart
     */
    public function loadItems(Cart $cart): Cart
    {
        return $cart->load(['items.product', 'items.variant']);
    }

    /**
     * @param Cart $to
     * @param Cart $from
     * @return Cart
     */
    public function merge(Cart $to, Cart $from): Cart
    {
        foreach ($from->items as $ci) {
            $same = $to->items()
                ->where('product_id', $ci->product_id)
                ->where('product_variant_id', $ci->product_variant_id)
                ->first();

            if ($same) {
                $same->increment('qty', $ci->qty);
            } else {
                $to->items()->create([
                    'product_id'         => $ci->product_id,
                    'product_variant_id' => $ci->product_variant_id,
                    'price_cents'        => $ci->price_cents,
                    'qty'                => $ci->qty,
                ]);
            }
        }

        $from->items()->delete();
        $from->delete();

        return $to->fresh('items');
    }

    /**
     * @param Cart $cart
     * @param array $attrs
     * @return CartItem
     */
    public function addItem(Cart $cart, array $attrs): CartItem
    {
        $item = $cart->items()
            ->where('product_id', $attrs['product_id'])
            ->where('product_variant_id', $attrs['product_variant_id'] ?? null)
            ->first();

        if ($item) {
            $item->increment('qty', (int) $attrs['qty']);
            return $item->fresh();
        }

        return $cart->items()->create($attrs);
    }

    /**
     * @param CartItem $item
     * @param int $qty
     * @return CartItem
     */
    public function updateItemQty(CartItem $item, int $qty): CartItem
    {
        $item->update(['qty' => $qty]);

        return $item->fresh();
    }

    /**
     * @param CartItem $item
     * @return void
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }
}
