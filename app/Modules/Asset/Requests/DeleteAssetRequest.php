<?php

namespace App\Modules\Asset\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;

class DeleteAssetRequest extends CommonRequest
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
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.DeleteAssetRequest');
    }
}
