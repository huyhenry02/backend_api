<?php

namespace App\Modules\Employee\Requests;

use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;

class UpdateCurriculumVitaeRequest extends CommonRequest
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
            'id' => 'required|uuid|exists:curriculum_vitaes,id',
            'nationality_id' => 'nullable|uuid|exists:nationalities,id',
            'name' => 'string',
            'email' => [
                'email',
                'max:255',
                Rule::unique('curriculum_vitaes', 'email')->ignore($this->request->get('id'), 'id')
            ],
            'phone_number' => [
                'string',
                'min:10',
                Rule::unique('curriculum_vitaes', 'phone_number')->ignore($this->request->get('id'), 'id')
            ],
            'dob' => 'nullable|string',
            'gender' => 'nullable|string|in:male,female',
            'country' => 'nullable|string',
            'marital' => 'nullable|boolean',
            'ethnic' => 'nullable|string',
            'region_id' => 'nullable|uuid|exists:religions,id',
            'identification' => [
                'string',
                'min:10',
                Rule::unique('curriculum_vitaes', 'identification')->ignore($this->request->get('id'), 'id')
            ],
            'place_of_issue' => 'nullable|string',
            'date_of_issue' => 'nullable|string',
            'tax_code' => 'nullable|string',
            'onboard_date' => 'nullable|date',
            'leader_id' => 'nullable|uuid|exists:employees,id',
            'subsidiary_id' => 'nullable|uuid|exists:hierarchies,id',
            'position_id' => 'nullable|uuid|exists:positions,id',
            'address' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'working_histories' => 'array',
            'working_histories.*.id' => 'nullable|uuid|exists:working_histories,id',
            'working_histories.*.start_date' => 'nullable|date',
            'working_histories.*.end_date' => 'nullable|date',
            'working_histories.*.position' => 'nullable|string',
            'working_histories.*.company' => 'nullable|string',
            'working_histories.*.is_deleted' => 'nullable|boolean',
            'media.new' => 'array',
            'media.new.identification_front' => 'nullable|uuid|exists:raw_media,id',
            'media.new.identification_back' => 'nullable|uuid|exists:raw_media,id',
            'media.new.face_image' => 'nullable|uuid|exists:raw_media,id',
            'media.new.fingerprint' => 'nullable|uuid|exists:raw_media,id',
            'media.delete' => 'array',
            'media.delete.*' => 'array',
            'media.delete.identification_front' => 'nullable|array',
            'media.delete.identification_front.*' => 'uuid|exists:media,uuid',
            'media.delete.identification_back' => 'nullable|array',
            'media.delete.identification_back.*' => 'uuid|exists:media,uuid',
            'media.delete.face_image' => 'nullable|array',
            'media.delete.face_image.*' => 'uuid|exists:media,uuid',
            'media.delete.fingerprint' => 'nullable|array',
            'media.delete.fingerprint.*' => 'uuid|exists:media,uuid',
        ];
    }

    public function attributes(): Application|array|string|Translator|ContractsApplication|null
    {
        return __('requests.UpdateCurriculumVitaeRequest');
    }
}
