<?php

namespace App\Modules\Employee\Requests;

use App\Enums\EmployeeRecordTypeEnum;
use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;

class CreateEmployeeLogRequest extends CommonRequest
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
        $type = implode(',', EmployeeRecordTypeEnum::values());
        return [
            'employee_id' => 'required|exist:employees,uuid',
            'type' => "required|string|in,$type",
            'data' => 'required|string',
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.CreateEmployeeLogRequest');
    }
}
