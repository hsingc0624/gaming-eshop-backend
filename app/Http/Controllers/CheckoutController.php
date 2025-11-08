<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    /**
     * @param CheckoutService $service
     */
    public function __construct(
        private CheckoutService $service
    ) {}

    /**
     * @param CheckoutRequest $r
     * @return JsonResponse
     */
    public function checkout(CheckoutRequest $r): JsonResponse
    {
        $order = $this->service->checkout($r->validated());

        return response()->json($order);
    }
}
