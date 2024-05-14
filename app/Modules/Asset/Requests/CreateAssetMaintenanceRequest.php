<?php

namespace App\Modules\Asset\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateAssetMaintenanceRequest extends CommonRequest
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
            'created_date' => 'required|date|date_format:Y-m-d H:i:s',
            'reason' => 'required|max:255',
            'description' => 'required|max:255',
            'proposal' => 'required|max:255',
            'created_by' => 'required|max:255',
            'code' => 'required|max:255',
            'causal' => 'required|max:255',
        ];
    }

}
