<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    /**
     * @param OrderRepositoryInterface $orders
     */
    public function __construct(
        private OrderRepositoryInterface $orders
    ) {}

    /**
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function index(int $userId, int $perPage): LengthAwarePaginator
    {
        return $this->orders->paginateForUser($userId, $perPage);
    }

    /**
     * @param string   $number
     * @param User|null $user
     * @return Order
     */
    public function show(string $number, ?User $user): Order
    {
        $order = $this->orders->findByNumberWithRelations($number);

        if ($user && ($order->user_id === $user->id || $user->can('manage orders'))) {
            return $order;
        }

        abort(403, 'Forbidden');
    }

    /**
     * @param string $number
     * @param array  $data
     * @return Order
     */
    public function update(string $number, array $data): Order
    {
        $order = $this->orders->findByNumberWithRelations($number);

        return $this->orders->updateFields($order, $data);
    }

    /**
     * @param string $number
     * @return Order
     */
    public function refund(string $number): Order
    {
        $order = $this->orders->findByNumberWithRelations($number);

        return $this->orders->refund($order);
    }
}
