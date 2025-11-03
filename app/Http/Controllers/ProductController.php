<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $q = Product::query()
            ->with(['images','variants'])
            ->where('is_active', true)
            ->when($r->query('category'), function ($qq, $slug) {
                $qq->whereHas('categories', fn($w) => $w->where('slug', $slug));
            })
            ->when($r->query('search'), function ($qq, $term) {
                $qq->where('name', 'like', '%'.$term.'%');
            })
            ->orderByDesc('id')
            ->paginate($r->integer('per_page') ?: 12);

        return response()->json($q);
    }

    public function show(string $slug)
    {
        $p = Product::with(['images','variants','categories'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($p);
    }

    public function categories()
    {
        return response()->json(
            Category::select('id','name','slug')->orderBy('name')->get()
        );
    }

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
}
