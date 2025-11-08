<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/** 
 * @class StoreUserRequest 
*/
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|min:2|max:80',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|string|exists:roles,name',
            'is_active' => 'sometimes|boolean',
            'password'  => 'sometimes|string|min:8',
        ];
    }
}
