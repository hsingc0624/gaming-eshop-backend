<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/** 
 * @class UpdateUserRequest 
*/
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'role'      => 'sometimes|string|exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
