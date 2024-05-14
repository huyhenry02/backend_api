<?php

namespace App\Modules\Log\Request;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;


class GetLogRequest extends CommonRequest
{
    public array $keys = [
        'employee',
        'health',
        'curriculum_vitae',
        'contract',
        'role',
        'appointment',
        'asset',
        'user',
        'asset_maintenance',
        'asset_delivery_history',
        'hierarchy',
    ];
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
            'id' => 'required|uuid',
            'key' => 'required|in:' . implode(',', $this->keys),
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.MasterDataRequest');
    }
}
