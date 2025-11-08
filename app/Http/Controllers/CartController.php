<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AttachCartRequest;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Requests\Cart\ShowCartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/** 
 * @class CartController
*/
class CartController extends Controller
{
    public function __construct(private CartService $service) {}

    /** @param AttachCartRequest $r @return JsonResponse */
    public function attach(AttachCartRequest $r): JsonResponse
    {
        $out = $this->service->attach($r->input('cart_token'), auth()->id());
        return response()->json($out);
    }

    /** 
     * @param AddToCartRequest $r @return JsonResponse 
    */
    public function add(AddToCartRequest $r): JsonResponse
    {
        $d = $r->validated();
        $item = $this->service->add(
            $d['cart_token'],
            (int)$d['product_id'],
            $d['product_variant_id'] ?? null,
            (int)$d['qty']
        );
        return response()->json($item);
    }

    /** 
     * @param UpdateCartItemRequest $r @param int $id @return JsonResponse 
     */
    public function update(UpdateCartItemRequest $r, int $id): JsonResponse
    {
        $item = $this->service->updateItem($id, (int)$r->validated()['qty']);
        return response()->json($item);
    }

    /**
     * @param int $id @return Response
    */
    public function remove(int $id): Response
    {
        $this->service->removeItem($id);
        return response()->noContent();
    }

    /** 
     * @param ShowCartRequest $r 
     * @return JsonResponse 
     */
    public function show(ShowCartRequest $r): JsonResponse
    {
        $cart = $this->service->show($r->query('cart_token'));
        return response()->json($cart);
    }
}
