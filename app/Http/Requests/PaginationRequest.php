<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;

class PaginationRequest extends CommonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.PaginationRequest');
    }
}
