<?php

namespace App\Modules\Employee\Requests;

use App\Enums\CommonStatusEnum;
use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;

class CreateUserRequest extends CommonRequest
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
            'employee_id' => [
                'required',
                'uuid',
                Rule::exists('employees', 'id')->where(function ($query) {
                    $query->where('status', CommonStatusEnum::ACTIVE);
                }),
                Rule::unique('users', 'employee_id')->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            ],
            'username' => [
                'required',
                'string',
                Rule::unique('users', 'username')->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            ],
            'password' => 'required|string|max:255',
            'role_id' => 'required|uuid|exists:roles,id',
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.CreateUserRequest');
    }
}
