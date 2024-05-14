<?php

namespace App\Modules\Asset\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ListAssetDeliveryRequest extends CommonRequest
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
            'asset_id' => 'required|uuid|exists:assets,id',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }
}
