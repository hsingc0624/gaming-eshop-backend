<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'string',
                Rule::in([
                    'pending',
                    'paid',
                    'processing',
                    'shipped',
                    'delivered',
                    'refunded',
                    'cancelled',
                ]),
            ],
            'admin_note' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
