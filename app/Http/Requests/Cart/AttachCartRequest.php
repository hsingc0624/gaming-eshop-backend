<?php
namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

/** 
 * @class AttachCartRequest 
*/
class AttachCartRequest extends FormRequest
{
    /** @return bool */
    public function authorize(): bool { return true; }

    /** @return array */
    public function rules(): array
    {
        return [
            'cart_token' => ['nullable','uuid'],
        ];
    }
}
