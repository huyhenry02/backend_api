<?php

namespace App\Modules\Employee\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateHealthRequest extends CommonRequest
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
            'id' => 'required|uuid|exists:healths,id',
            'blood_pressure' => 'numeric',
            'heartbeat' => 'numeric',
            'height' => 'numeric',
            'weight' => 'numeric',
            'blood_group' => 'in:A+,B+,AB+,O+,A-,B-,AB-,O-|max:3',
            'note' => 'string|max:255',
            'media' => 'array',
            'media.new' => 'array',
            'media.new.*' => 'uuid|exists:raw_media,id',
            'media.delete' => 'array',
            'media.delete.*' => 'array',
            'media.delete.*.*' => 'uuid|exists:media,uuid'
        ];
    }
    public function attributes()
    {
        return __('requests.UpdateHealthRequest');
    }
}
