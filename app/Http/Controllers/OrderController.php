<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->with(['items.product','addresses'])
            ->orderByDesc('id')
            ->paginate($r->integer('per_page') ?: 15);

        return response()->json($orders);
    }

    public function show(string $number)
    {
        $order = Order::with(['items.product','addresses'])
            ->where('number', $number)
            ->firstOrFail();

        if ($order->user_id !== auth()->id() && !auth()->user()->can('manage orders')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($order);
    }

    public function update(Request $r, string $number)
    {
        $order = Order::where('number', $number)->firstOrFail();

        $data = $r->validate([
            'status' => [
                'sometimes','string',
                Rule::in(['pending','paid','processing','shipped','delivered','refunded','cancelled']),
            ],
            'admin_note' => 'sometimes|nullable|string|max:2000',
        ]);

        if (array_key_exists('status', $data)) {
            $order->status = $data['status'];
        }
        if (array_key_exists('admin_note', $data)) {
            $order->admin_note = $data['admin_note'];
        }
        $order->save();

        $order->load(['items.product','addresses']);
        return response()->json($order);
    }

    public function refund(Request $r, string $number)
    {
        $order = Order::where('number', $number)->firstOrFail();

        $order->status = 'refunded';
        $order->save();

        return response()->json([
            'message' => 'Order refunded',
            'order' => $order->fresh(['items.product','addresses']),
        ]);
    }
}
