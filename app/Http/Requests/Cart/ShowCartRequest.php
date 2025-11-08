<?php
namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

/** 
 * @class ShowCartRequest 
*/
class ShowCartRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cart_token' => ['nullable','uuid'],
        ];
    }
}
