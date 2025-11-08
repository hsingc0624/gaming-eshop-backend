<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\CartRepositoryInterface;
use Illuminate\Support\Str;

class CartService
{
    /**
     * @param CartRepositoryInterface $carts
     */
    public function __construct(
        private CartRepositoryInterface $carts
    ) {}

    /**
     * @param string|null $inputToken
     * @param int|null    $userId
     *
     * @return array{cart_token:string, cart:mixed}
     */
    public function attach(?string $inputToken, ?int $userId): array
    {
        $cart = $inputToken ? $this->carts->findByToken($inputToken) : null;

        if ($userId) {
            if ($cart && $cart->user_id === null) {
                $existing = $this->carts->findByUserId($userId);

                if ($existing) {
                    $cart = $this->carts->merge($existing, $cart);
                } else {
                    $cart->update(['user_id' => $userId]);
                }
            } elseif ($cart && $cart->user_id !== $userId) {
                $cart = $this->carts->findByUserId($userId)
                    ?? $this->carts->create([
                        'user_id'    => $userId,
                        'cart_token' => (string) Str::uuid(),
                    ]);
            } else {
                $cart = $cart
                    ?? ($this->carts->findByUserId($userId)
                    ?? $this->carts->create([
                        'user_id'    => $userId,
                        'cart_token' => $inputToken ?: (string) Str::uuid(),
                    ]));
            }
        } else {
            $cart = $cart ?? $this->carts->create([
                'cart_token' => $inputToken ?: (string) Str::uuid(),
                'user_id'    => null,
            ]);
        }

        $cart = $this->carts->loadItems($cart);

        return [
            'cart_token' => $cart->cart_token,
            'cart'       => $cart,
        ];
    }

    /**
     * @param string   $cartToken
     * @param int      $productId
     * @param int|null $variantId
     * @param int      $qty
     *
     * @return CartItem
     */
    public function add(string $cartToken, int $productId, ?int $variantId, int $qty): CartItem
    {
        $cart = $this->carts->findByToken($cartToken) ?? abort(404);

        if (auth()->check() && $cart->user_id && $cart->user_id !== auth()->id()) {
            abort(403, 'Cart does not belong to current user.');
        }

        $product = Product::findOrFail($productId);

        $price = $product->sale_price_cents ?? $product->price_cents;

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $price   = $variant->price_cents;
        }

        $item = $this->carts->addItem($cart, [
            'product_id'         => $productId,
            'product_variant_id' => $variantId,
            'price_cents'        => $price,
            'qty'                => $qty,
        ]);

        return $item->load(['product', 'variant']);
    }

    /**
     * @param int $itemId
     * @param int $qty
     *
     * @return CartItem
     */
    public function updateItem(int $itemId, int $qty): CartItem
    {
        $item = CartItem::with('cart')->findOrFail($itemId);

        if (auth()->check() && $item->cart?->user_id && $item->cart->user_id !== auth()->id()) {
            abort(403, 'Cart does not belong to current user.');
        }

        $item = $this->carts->updateItemQty($item, $qty);

        return $item->load(['product', 'variant']);
    }

    /**
     * @param int $itemId
     *
     * @return void
     */
    public function removeItem(int $itemId): void
    {
        $item = CartItem::with('cart')->findOrFail($itemId);

        if (auth()->check() && $item->cart?->user_id && $item->cart->user_id !== auth()->id()) {
            abort(403, 'Cart does not belong to current user.');
        }

        $this->carts->removeItem($item);
    }

    /**
     * @param string|null $token
     *
     * @return mixed
     */
    public function show(?string $token)
    {
        $cart = null;

        if ($token) {
            $cart = $this->carts->findByToken($token);

            if ($cart) {
                $cart = $this->carts->loadItems($cart);
            }
        }

        if (!$cart && auth()->check()) {
            $cart = $this->carts->findByUserId(auth()->id());

            if ($cart) {
                $cart = $this->carts->loadItems($cart);
            }
        }

        if ($cart && auth()->check() && $cart->user_id && $cart->user_id !== auth()->id()) {
            return ['items' => []];
        }

        return $cart ?: ['items' => []];
    }
}
