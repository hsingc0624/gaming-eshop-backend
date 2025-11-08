<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array>
     */
    public function rules(): array
    {
        return [
            'cart_token'     => ['required', 'uuid'],
            'shipping'       => ['required', 'array'],
            'billing'        => ['required', 'array'],
            'payment_method' => ['nullable', 'string'],
            'coupon'         => ['nullable', 'string'],
        ];
    }
}
