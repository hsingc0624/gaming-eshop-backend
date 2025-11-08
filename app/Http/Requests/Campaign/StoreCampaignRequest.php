<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
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
            'name'    => ['required', 'string', 'max:200'],
            'subject' => ['required', 'string', 'max:200'],
            'html'    => ['required', 'string'],
            'segment' => ['nullable', 'string', 'max:100'],
        ];
    }
}
