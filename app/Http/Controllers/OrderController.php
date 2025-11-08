<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\IndexOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Requests\Order\RefundOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * @param OrderService $service
     */
    public function __construct(
        private OrderService $service
    ) {}

    /**
     * @param IndexOrderRequest $r
     * @return JsonResponse
     */
    public function index(IndexOrderRequest $r): JsonResponse
    {
        $perPage = (int) ($r->integer('per_page') ?: 15);

        $orders = $this->service->index((int) auth()->id(), $perPage);

        return response()->json($orders);
    }

    /**
     * @param string $number
     * @return JsonResponse
     */
    public function show(string $number): JsonResponse
    {
        $order = $this->service->show($number, auth()->user());

        return response()->json($order);
    }

    /**
     * @param UpdateOrderRequest $r
     * @param string             $number
     * @return JsonResponse
     */
    public function update(UpdateOrderRequest $r, string $number): JsonResponse
    {
        $order = $this->service->update($number, $r->validated());

        return response()->json($order);
    }

    /**
     * @param RefundOrderRequest $r
     * @param string             $number
     * @return JsonResponse
     */
    public function refund(RefundOrderRequest $r, string $number): JsonResponse
    {
        $order = $this->service->refund($number);

        return response()->json([
            'message' => 'Order refunded',
            'order'   => $order,
        ]);
    }

    /**
     * @param  string  $number  The unique order number identifier.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function invoice(string $number)
    {
        $order = $this->service->show($number, auth()->user());

        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order]);

        return $pdf->download("invoice-{$order->number}.pdf");
    }
}
