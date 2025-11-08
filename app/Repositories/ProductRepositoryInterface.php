<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * @package App\Repositories
 */
interface ProductRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator;

    /**
     * @param string $slug
     * @return Product
     */
    public function findBySlug(string $slug): Product;

    /**
     * @return Collection
     */
    public function listCategories(): Collection;

    /**
     * @param array $data
     * @return Product
     */
    public function createWithRelations(array $data): Product;

    /**
     * @param int $id
     * @return void
     */
    public function deleteCascade(int $id): void;
}
