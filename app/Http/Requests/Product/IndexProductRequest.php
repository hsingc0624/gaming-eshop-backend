<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @class IndexProductRequest
 * @package App\Http\Requests\Product
 * @description Validation rules for product listing filters.
 */
class IndexProductRequest extends FormRequest
{
    /** @return bool */
    public function authorize(): bool { return true; }

    /** @return array */
    public function rules(): array
    {
        return [
            'per_page'   => 'nullable|integer|min:1|max:100',
            'search'     => 'nullable|string|max:255',
            'sort'       => 'nullable|in:latest,price_asc,price_desc,name_asc',
            'category'   => 'nullable',
            'category.*' => 'string',
            'min_price'  => 'nullable|numeric|min:0',
            'max_price'  => 'nullable|numeric|min:0',
        ];
    }
}
