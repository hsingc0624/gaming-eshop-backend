<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
     /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function index(Request $r)
    {
        $perPage = (int) ($r->integer('per_page') ?: 12);
        $search  = $r->query('search');
        $sort    = $r->query('sort', 'latest');

        $catsParam = $r->query('category');
        $catSlugs  = is_array($catsParam) ? $catsParam : (array) ($catsParam ?? []);

        $min = $r->filled('min_price') ? (int) round(((float) $r->query('min_price')) * 100) : null;
        $max = $r->filled('max_price') ? (int) round(((float) $r->query('max_price')) * 100) : null;

        $q = Product::query()
            ->with(['images','variants'])
            ->where('is_active', true)
            ->when(!empty($catSlugs), function ($qq) use ($catSlugs) {
                $qq->whereHas('categories', fn($w) => $w->whereIn('slug', $catSlugs));
            })
            ->when($search, function ($qq, $term) {
                $qq->where('name', 'like', '%'.$term.'%');
            })
            ->when($min !== null, function ($qq) use ($min) {
                $qq->whereRaw('COALESCE(sale_price_cents, price_cents) >= ?', [$min]);
            })
            ->when($max !== null, function ($qq) use ($max) {
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

        $paginated = $q->paginate($perPage);

        return response()->json($paginated);
    }


    /**
     * @param  string  $slug
     * @return JsonResponse
     */
    public function show(string $slug)
    {
        $p = Product::with(['images','variants','categories'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($p);
    }

    /**
     * @return JsonResponse
     */
    public function categories()
    {
        return response()->json(
            Category::select('id','name','slug')->orderBy('name')->get()
        );
    }

    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price_cents' => 'required|integer|min:0',
            'sale_price_cents' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'categories' => 'array',
            'categories.*' => 'string',
            'images' => 'array',
            'images.*.url' => 'required|string',
            'images.*.position' => 'nullable|integer',
            'variants' => 'array',
            'variants.*.sku' => 'required|string|unique:product_variants,sku',
            'variants.*.price_cents' => 'required|integer|min:0',
            'variants.*.stock' => 'required|integer',
            'variants.*.options' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($data) {
            $p = Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'price_cents' => $data['price_cents'],
                'sale_price_cents' => $data['sale_price_cents'] ?? null,
                'is_active' => $data['is_active'] ?? false,
            ]);

            if (!empty($data['categories'])) {
                $catIds = Category::whereIn('slug', $data['categories'])->pluck('id')->all();
                $p->categories()->sync($catIds);
            }

            foreach ($data['images'] ?? [] as $i) {
                $p->images()->create([
                    'url' => $i['url'],
                    'position' => $i['position'] ?? 0
                ]);
            }

            foreach ($data['variants'] ?? [] as $v) {
                $p->variants()->create([
                    'sku' => $v['sku'],
                    'options' => isset($v['options']) ? json_encode($v['options']) : null,
                    'price_cents' => $v['price_cents'],
                    'stock' => $v['stock'],
                ]);
            }

            return $p->load('images','variants','categories');
        });
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $p = Product::with(['images','variants','categories'])->findOrFail($id);

            $p->categories()->sync([]); 
            $p->images()->delete();
            $p->variants()->delete();

            $p->delete();

            return response()->json(['message' => 'Deleted']);
        });
    }
}
