<?php

namespace App\Modules\Employee\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;

class SearchEmployeeRequest extends CommonRequest
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
            'id' => 'required|uuid|exists:employees,id',
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.SearchEmployeeLogRequest');
    }
}
