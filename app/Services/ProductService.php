<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @class ProductService
 * @package App\Services
 * @description Handles business logic for product operations.
 */
class ProductService
{
    /**
     * @param ProductRepositoryInterface $products
     */
    public function __construct(private ProductRepositoryInterface $products) {}

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function list(array $filters): LengthAwarePaginator
    {
        return $this->products->paginate($filters);
    }

    /**
     * @param string $slug
     * @return Product
     */
    public function show(string $slug): Product
    {
        return $this->products->findBySlug($slug);
    }

    /**
     * @return Collection
     */
    public function categories(): Collection
    {
        return $this->products->listCategories();
    }

    /**
     * @param array $payload
     * @return Product
     */
    public function create(array $payload): Product
    {
        return DB::transaction(fn() => $this->products->createWithRelations($payload));
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        DB::transaction(fn() => $this->products->deleteCascade($id));
    }
}
