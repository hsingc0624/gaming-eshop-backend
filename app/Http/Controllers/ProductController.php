<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

/**
 * @class ProductController
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * @param ProductService $service
     */
    public function __construct(private ProductService $service) {}

    /**
     * @param IndexProductRequest $r
     * @return JsonResponse
     */
    public function index(IndexProductRequest $r): JsonResponse
    {
        $paginated = $this->service->list($r->validated() + $r->query());
        return response()->json($paginated);
    }

    /**
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        return response()->json($this->service->show($slug));
    }

    /**
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        return response()->json($this->service->categories());
    }

    /**
     * @param StoreProductRequest $r
     * @return JsonResponse
     */
    public function store(StoreProductRequest $r): JsonResponse
    {
        $p = $this->service->create($r->validated());
        return response()->json($p, 201);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
