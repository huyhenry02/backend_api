<?php

namespace App\Modules\GlobalSearch\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class GlobalSearchRequest extends CommonRequest
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
            'keyword' => 'required',
            'module' => 'nullable|in:asset,employee,role,asset_maintenance,asset_delivery',
            'module_parent_id' => 'nullable|uuid',
        ];
    }
}
