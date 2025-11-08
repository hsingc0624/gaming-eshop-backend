<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;

interface CartRepositoryInterface
{
    /**
     * @param string $token
     * @return Cart|null
     */
    public function findByToken(string $token): ?Cart;

    /**
     * @param int $userId
     * @return Cart|null
     */
    public function findByUserId(int $userId): ?Cart;

    /**
     * @param array $attrs
     * @return Cart
     */
    public function create(array $attrs): Cart;

    /**
     * @param Cart $cart
     * @return Cart
     */
    public function loadItems(Cart $cart): Cart;

    /**
     * @param Cart $to
     * @param Cart $from
     * @return Cart
     */
    public function merge(Cart $to, Cart $from): Cart;

    /**
     * @param Cart  $cart
     * @param array $attrs
     * @return CartItem
     */
    public function addItem(Cart $cart, array $attrs): CartItem;

    /**
     * @param CartItem $item
     * @param int      $qty
     * @return CartItem
     */
    public function updateItemQty(CartItem $item, int $qty): CartItem;

    /**
     * @param CartItem $item
     * @return void
     */
    public function removeItem(CartItem $item): void;
}
