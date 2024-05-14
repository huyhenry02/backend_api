<?php

namespace App\Modules\Asset\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateAssetRequest extends CommonRequest
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
            'name' => 'required|max:255',
            'management_code' => 'required|max:255',
            'management_unit' => 'required|max:255',
            'original_price' => 'required|numeric',
            'residual_price' => 'required|numeric',
            'insurance_contract' => 'required|max:255',
            'asset_images' => 'required|uuid|exists:raw_media,id'
        ];
    }
}
