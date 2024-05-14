<?php

namespace App\Modules\Asset\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;

class UpdateAssetRequest extends CommonRequest
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
            'name' => 'max:255',
            'management_code' => 'max:255',
            'management_unit' => 'max:255',
            'original_price' => 'numeric',
            'residual_price' => 'numeric',
            'insurance_contract' => 'max:255',
            'status' => 'string|in:active,inactive',
            'media' => 'array',
            'media.new' => 'array',
            'media.new.*' => 'uuid|exists:raw_media,id',
            'media.delete' => 'array',
            'media.delete.*' => 'array',
            'media.delete.*.*' => 'uuid|exists:media,uuid'
        ];
    }
    public function attributes(): Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        return __('requests.UpdateAssetRequest');
    }
}
