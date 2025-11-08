<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * @param int   $userId
     * @param int   $perPage
     * @return LengthAwarePaginator
     */
    public function paginateForUser(int $userId, int $perPage): LengthAwarePaginator;

    /**
     * @param string $number
     * @return Order
     */
    public function findByNumberWithRelations(string $number): Order;

    /**
     * @param Order $order
     * @param array $data
     * @return Order
     */
    public function updateFields(Order $order, array $data): Order;

    /**
     * @param Order $order
     * @return Order
     */
    public function refund(Order $order): Order;
}
