<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    /**
     * @param array $filters {
     *     @type int|null    $per_page
     *     @type string|null $search
     *     @type string|null $sort        latest|price_asc|price_desc|name_asc
     *     @type array|string|null $category
     *     @type float|int|string|null $min_price
     *     @type float|int|string|null $max_price
     * }
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $perPage  = (int)($filters['per_page'] ?? 12);
        $search   = $filters['search'] ?? null;
        $sort     = $filters['sort'] ?? 'latest';

        $catsParam = $filters['category'] ?? [];
        $catSlugs  = is_array($catsParam) ? $catsParam : (array)($catsParam ?: []);

        $min = isset($filters['min_price']) ? (int) round(((float)$filters['min_price']) * 100) : null;
        $max = isset($filters['max_price']) ? (int) round(((float)$filters['max_price']) * 100) : null;

        $q = Product::query()
            ->with(['images','variants'])
            ->where('is_active', true)
            ->when(!empty($catSlugs), function (Builder $qq) use ($catSlugs) {
                $qq->whereHas('categories', fn($w) => $w->whereIn('slug', $catSlugs));
            })
            ->when($search, function (Builder $qq, $term) {
                $qq->where('name', 'like', '%'.$term.'%');
            })
            ->when($min !== null, function (Builder $qq) use ($min) {
                $qq->whereRaw('COALESCE(sale_price_cents, price_cents) >= ?', [$min]);
            })
            ->when($max !== null, function (Builder $qq) use ($max) {
                $qq->whereRaw('COALESCE(sale_price_cents, price_cents) <= ?', [$max]);
            });

        switch ($sort) {
            case 'price_asc':
                $q->orderByRaw('COALESCE(sale_price_cents, price_cents) ASC');
                break;
            case 'price_desc':
                $q->orderByRaw('COALESCE(sale_price_cents, price_cents) DESC');
                break;
            case 'name_asc':
                $q->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $q->orderByDesc('id');
        }

        return $q->paginate($perPage);
    }

    /**
     * @param string $slug
     * @return Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBySlug(string $slug): Product
    {
        return Product::with(['images','variants','categories'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * @return Collection<int, \App\Models\Category>
     */
    public function listCategories(): Collection
    {
        return Category::select('id','name','slug')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param array $data
     * @return Product
     */
    public function createWithRelations(array $data): Product
    {
        $p = Product::create([
            'name'             => $data['name'],
            'slug'             => $data['slug'],
            'description'      => $data['description'] ?? null,
            'price_cents'      => $data['price_cents'],
            'sale_price_cents' => $data['sale_price_cents'] ?? null,
            'is_active'        => $data['is_active'] ?? false,
        ]);

        if (!empty($data['categories'])) {
            $catIds = Category::whereIn('slug', $data['categories'])->pluck('id')->all();
            $p->categories()->sync($catIds);
        }

        foreach ($data['images'] ?? [] as $i) {
            $p->images()->create([
                'url'      => $i['url'],
                'position' => $i['position'] ?? 0,
            ]);
        }

        foreach ($data['variants'] ?? [] as $v) {
            $p->variants()->create([
                'sku'         => $v['sku'],
                'options'     => $v['options'] ?? null,
                'price_cents' => $v['price_cents'],
                'stock'       => $v['stock'],
            ]);
        }

        return $p->load('images','variants','categories');
    }

    /**
     * @param int $id
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteCascade(int $id): void
    {
        $p = Product::with(['images','variants','categories'])->findOrFail($id);
        $p->categories()->sync([]);
        $p->images()->delete();
        $p->variants()->delete();
        $p->delete();
    }
}
