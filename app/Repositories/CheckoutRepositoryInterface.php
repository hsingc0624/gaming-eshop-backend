<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Order;

interface CheckoutRepositoryInterface
{
    /**
     * @param string $token
     * @return Cart|null
     */
    public function findCartByToken(string $token): ?Cart;

    /**
     * @param array $data
     * @return Order
     */
    public function createOrder(array $data): Order;
}
