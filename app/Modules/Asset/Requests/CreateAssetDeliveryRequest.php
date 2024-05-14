<?php

namespace App\Modules\Asset\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateAssetDeliveryRequest extends CommonRequest
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
            'receiver' => 'required|max:255',
            'deliver' => 'required|max:255',
            'reason' => 'required|max:255',
            'place_of_use' => 'required|max:255',
            'attachments' => 'required|max:255',
            'code' => 'required|max:255',
        ];
    }
}
