<?php

namespace App\Modules\Appointment\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Application;

class UpdateAppointmentStatusRequest extends CommonRequest
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
            'id' => 'required|uuid|exists:appointments,id',
            'status' => 'required|in:approved,processing,completed,rejected',
            'reject_reason' => 'required_if:status,rejected',
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.CreateAppointmentRequest');
    }
}
