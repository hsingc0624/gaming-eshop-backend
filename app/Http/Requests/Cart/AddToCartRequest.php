<?php
namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

/** 
 * @class AddToCartRequest 
*/
class AddToCartRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cart_token'         => ['required','uuid'],
            'product_id'         => ['required','exists:products,id'],
            'product_variant_id' => ['nullable','exists:product_variants,id'],
            'qty'                => ['required','integer','min:1'],
        ];
    }
}
