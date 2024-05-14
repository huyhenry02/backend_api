<?php

namespace App\Modules\RolePermission\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;

class UpdateRoleRequest extends CommonRequest
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
            'id' => 'required|uuid|exists:roles,id',
            'description' => 'required|string|max:100|min:3',
            'status' => 'required|string|in:active,inactive',
            "permissions" => "nullable|array",
        ];
    }
    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.RoleRequest');
    }
}
