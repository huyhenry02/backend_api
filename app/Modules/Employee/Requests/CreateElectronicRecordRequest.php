<?php

namespace App\Modules\Employee\Requests;

use App\Enums\CommonStatusEnum;
use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateElectronicRecordRequest extends CommonRequest
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
            'curriculum_vitae' => 'required|array',
            'curriculum_vitae.employee_id' => 'nullable|uuid|exists:employees,id|unique:curriculum_vitaes,employee_id',
            'curriculum_vitae.nationality_id' => 'nullable|uuid|exists:nationalities,id',
            'curriculum_vitae.name' => 'required|string',
            'curriculum_vitae.email' => 'required|email|string|unique:curriculum_vitaes,email',
            'curriculum_vitae.phone_number' => 'required|string|min:10|unique:curriculum_vitaes,phone_number',
            'curriculum_vitae.dob' => 'nullable|string',
            'curriculum_vitae.gender' => 'nullable|string|in:male,female',
            'curriculum_vitae.country' => 'nullable|string',
            'curriculum_vitae.marital' => 'nullable|boolean',
            'curriculum_vitae.ethnic' => 'nullable|string',
            'curriculum_vitae.region' => 'nullable|uuid|exists:religions,id',
            'curriculum_vitae.identification' => 'required|string|unique:curriculum_vitaes,identification',
            'curriculum_vitae.place_of_issue' => 'nullable|string',
            'curriculum_vitae.date_of_issue' => 'nullable|string',
            'curriculum_vitae.identification_front' => 'nullable|uuid|exists:raw_media,id',
            'curriculum_vitae.identification_back' => 'nullable|uuid|exists:raw_media,id',
            'curriculum_vitae.face_image' => 'nullable|uuid|exists:raw_media,id',
            'curriculum_vitae.fingerprint' => 'nullable|uuid|exists:raw_media,id',
            'curriculum_vitae.tax_code' => 'nullable|string',
            'curriculum_vitae.onboard_date' => 'nullable|date',
            'curriculum_vitae.leader_id' => 'nullable|uuid|exists:employees,id',
            'curriculum_vitae.subsidiary_id' => 'nullable|uuid|exists:hierarchies,id',
            'curriculum_vitae.position_id' => 'nullable|uuid|exists:positions,id',
            'curriculum_vitae.address' => 'nullable|string',
            'curriculum_vitae.bank_account_number' => 'nullable|string',
            'curriculum_vitae.bank_account_name' => 'nullable|string',
            'curriculum_vitae.bank_name' => 'nullable|string',
            'curriculum_vitae.bank_branch' => 'nullable|string',
            'curriculum_vitae.working_histories' => 'array',
            'curriculum_vitae.working_histories.*.start_date' => 'nullable|date',
            'curriculum_vitae.working_histories.*.end_date' => 'nullable|date',
            'curriculum_vitae.working_histories.*.position' => 'nullable|string',
            'curriculum_vitae.working_histories.*.company' => 'nullable|string',
            'contract' => 'required|array',
            'contract.contract_type_id' => 'required|uuid|exists:contract_types,id',
            'contract.contract_files' => 'required|uuid|exists:raw_media,id',
            'contract.department_id' => 'nullable|uuid|exists:hierarchies,id',
            'contract.position_id' => 'nullable|uuid|exists:positions,id',
            'contract.function' => 'nullable|string',
            'contract.rank' => 'nullable|string',
            'contract.skill_coefficient' => 'nullable|string',
            'workplace' => 'nullable|uuid|exists:cities,id',
            'contract.employment_type_id' => 'nullable|uuid|exists:employments,id',
            'contract.effective_date' => 'nullable|date',
            'contract.signed_date' => 'nullable|date',
            'contract.signer' => 'nullable|uuid|exists:employees,id',
            'contract.digital_signature' => 'nullable|in:' . implode(",", CommonStatusEnum::values()),
            'contract.apply_from_date' => 'nullable|date',
            'contract.note' => 'nullable|string',
            'contract.payment_type' => 'nullable|string',
            'contract.salary' => 'nullable|numeric',
            'contract.insurance_book_number' => 'nullable|string|max:63',
            'contract.insurance_book_status' => 'nullable|in:' . implode(",", CommonStatusEnum::values()),
            'contract.insurers' => 'nullable|string',
            'contract.insurance_card_number' => 'nullable|string|max:63',
            'contract.insurance_city_code' => 'nullable|string|max:63',
            'contract.medical_examination_place' => 'nullable|string|max:127',
            'contract.card_received_date' => 'nullable|date',
            'contract.card_returned_date' => 'nullable|date',
            'contract.contract_health_records' => 'required|uuid|exists:raw_media,id',
            'contract.contract_working_histories' => 'array',
            'contract.contract_working_histories.*.worked_from_date' => 'nullable|date',
            'contract.contract_working_histories.*.worked_to_date' => 'nullable|date',
            'contract.contract_working_histories.*.from_department' => 'nullable|uuid|exists:hierarchies,id',
            'contract.contract_working_histories.*.to_department' => 'nullable|uuid|exists:hierarchies,id',
            'contract.contract_working_histories.*.reason' => 'nullable|string|max:512',
            'contract.contract_working_histories.*.job_transfer_proofs' => 'nullable|uuid|exists:raw_media,id',
            'contract.contract_allowances' => 'array',
            'contract.contract_allowances.*.allowance_id' => 'nullable|uuid|exists:allowances,id',
            'contract.contract_allowances.*.benefit' => 'nullable|numeric',
            'contract.contract_insurance_processed_histories' => 'array',
            'contract.contract_insurance_processed_histories.*.insurance_policy_id' => 'nullable|uuid|exists:insurance_policies,id',
            'contract.contract_insurance_processed_histories.*.received_date' => 'nullable|date',
            'contract.contract_insurance_processed_histories.*.completed_date' => 'nullable|date',
            'contract.contract_insurance_processed_histories.*.refund_amount' => 'nullable|numeric',
            'contract.contract_insurance_processed_histories.*.refunded_date' => 'nullable|date',
            'health' => 'required|array',
            'health.blood_pressure' => 'nullable|numeric',
            'health.heartbeat' => 'nullable|numeric',
            'health.height' => 'nullable|numeric',
            'health.weight' => 'nullable|numeric',
            'health.blood_group' => 'nullable|in:A+,B+,AB+,O+,A-,B-,AB-,O-|max:3',
            'health.note' => 'required|string|max:255',
            'health.health_records' => 'required|uuid|exists:raw_media,id'
        ];
    }
}
