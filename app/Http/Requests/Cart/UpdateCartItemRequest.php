<?php
namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

/** 
 * @class UpdateCartItemRequest 
*/
class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'qty' => ['required','integer','min:1'],
        ];
    }
}
