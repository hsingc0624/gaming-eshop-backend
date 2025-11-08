<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function paginateForUser(int $userId, int $perPage): LengthAwarePaginator
    {
        return Order::query()
            ->where('user_id', $userId)
            ->with(['items.product', 'addresses'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function findByNumberWithRelations(string $number): Order
    {
        return Order::with(['items.product', 'addresses'])
            ->where('number', $number)
            ->firstOrFail();
    }

    /**
     * @inheritDoc
     */
    public function updateFields(Order $order, array $data): Order
    {
        if (array_key_exists('status', $data)) {
            $order->status = $data['status'];
        }

        if (array_key_exists('admin_note', $data)) {
            $order->admin_note = $data['admin_note'];
        }

        $order->save();

        return $order->fresh(['items.product', 'addresses']);
    }

    /**
     * @inheritDoc
     */
    public function refund(Order $order): Order
    {
        $order->status = 'refunded';
        $order->save();

        return $order->fresh(['items.product', 'addresses']);
    }
}
