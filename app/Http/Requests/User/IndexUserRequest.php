<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/** 
 * @class IndexUserRequest
 */
class IndexUserRequest extends FormRequest
{
    /** 
     * @return bool 
     */
    public function authorize(): bool { return true; }

    /** 
     * @return array
     */
    public function rules(): array
    {
        return [
            'role'   => 'nullable|string|exists:roles,name',
            'status' => 'nullable|in:active,inactive',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
