<?php

namespace App\Modules\MasterData\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;

class GetListMultiKeyMasterDataRequest extends CommonRequest
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
        $invalidKey = [
            'position',
            'employment',
            'insurance_policy',
            'nationality',
            'unit_level',
            'allowance',
            'city',
            'title',
            'religion',
            'contract_type',
            'salary_type',
        ];
        return [
            'keys' => 'required|array',
            'keys.*' => 'in:' . implode(',', $invalidKey),
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.MasterDataRequest');
    }
}
