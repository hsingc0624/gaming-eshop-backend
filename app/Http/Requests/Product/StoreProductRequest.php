<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @class StoreProductRequest
 * @package App\Http\Requests\Product
 * @description Validation rules for product creation.
 */
class StoreProductRequest extends FormRequest
{
    /** @return bool */
    public function authorize(): bool { return true; }

    /** @return array */
    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'slug'               => 'required|string|max:255|unique:products,slug',
            'description'        => 'nullable|string',
            'price_cents'        => 'required|integer|min:0',
            'sale_price_cents'   => 'nullable|integer|min:0',
            'is_active'          => 'boolean',
            'categories'         => 'array',
            'categories.*'       => 'string',
            'images'             => 'array',
            'images.*.url'       => 'required|string',
            'images.*.position'  => 'nullable|integer',
            'variants'                  => 'array',
            'variants.*.sku'            => 'required|string|unique:product_variants,sku',
            'variants.*.price_cents'    => 'required|integer|min:0',
            'variants.*.stock'          => 'required|integer',
            'variants.*.options'        => 'nullable|array',
        ];
    }
}
