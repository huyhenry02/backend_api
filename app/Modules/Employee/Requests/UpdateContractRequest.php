<?php

namespace App\Modules\Employee\Requests;

use App\Enums\CommonStatusEnum;
use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateContractRequest extends CommonRequest
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
            'id' => 'required|uuid|exists:contracts,id',
            'contract_type_id' => 'uuid|exists:contract_types,id',
            'department_id' => 'nullable|uuid|exists:hierarchies,id',
            'position_id' => 'nullable|uuid|exists:positions,id',
            'function' => 'nullable|string',
            'rank' => 'nullable|string',
            'skill_coefficient' => 'nullable|numeric|between:0,9.99',
            'workplace' => 'nullable|uuid|exists:cities,id',
            'employment_type_id' => 'nullable|uuid|exists:employments,id',
            'effective_date' => 'nullable|date',
            'signed_date' => 'nullable|date',
            'signer' => 'nullable|string',
            'digital_signature' => 'nullable|in:' . implode(",", CommonStatusEnum::values()),
            'apply_from_date' => 'nullable|date',
            'note' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'contract_allowances' => 'array',
            'contract_allowances.*.id' => 'nullable|uuid|exists:contract_allowances,id',
            'contract_allowances.*.allowance_id' => 'nullable|uuid|exists:allowances,id',
            'contract_allowances.*.benefit' => 'nullable|numeric',
            'contract_allowances.*.is_deleted' => 'nullable|boolean',
            'insurance_book_number' => 'nullable|string|max:63',
            'insurance_book_status' => 'nullable|in:' . implode(",", CommonStatusEnum::values()),
            'insurers' => 'nullable|string',
            'insurance_card_number' => 'nullable|string|max:63',
            'insurance_city_code' => 'nullable|string|max:63',
            'medical_examination_place' => 'nullable|string|max:127',
            'card_received_date' => 'nullable|date',
            'card_returned_date' => 'nullable|date',
            'contract_working_histories' => 'array',
            'contract_working_histories.*.id' => 'nullable|uuid|exists:contract_working_histories,id',
            'contract_working_histories.*.worked_from_date' => 'nullable|date',
            'contract_working_histories.*.worked_to_date' => 'nullable|date',
            'contract_working_histories.*.from_department' => 'nullable|uuid|exists:hierarchies,id',
            'contract_working_histories.*.to_department' => 'nullable|uuid|exists:hierarchies,id',
            'contract_working_histories.*.reason' => 'nullable|string|max:512',
            'contract_working_histories.*.is_deleted' => 'nullable|boolean',
            'media' => 'array',
            'media.new' => 'array',
            'media.new.*' => 'uuid|exists:raw_media,id',
            'media.delete' => 'array',
            'media.delete.*' => 'array',
            'media.delete.*.*' => 'uuid|exists:media,uuid',
            'contract_working_histories.*.media' => 'array',
            'contract_working_histories.*.media.new' => 'array',
            'contract_working_histories.*.media.new.*' => 'uuid|exists:raw_media,id',
            'contract_working_histories.*.media.delete' => 'array',
            'contract_working_histories.*.media.delete.*' => 'array',
            'contract_working_histories.*.media.delete.*.*' => 'uuid|exists:media,uuid',
            'contract_insurance_processed_histories' => 'array',
            'contract_insurance_processed_histories.*.id' => 'nullable|uuid|exists:contract_insurance_processed_histories,id',
            'contract_insurance_processed_histories.*.insurance_policy_id' => 'nullable|uuid|exists:insurance_policies,id',
            'contract_insurance_processed_histories.*.received_date' => 'nullable|date',
            'contract_insurance_processed_histories.*.completed_date' => 'nullable|date',
            'contract_insurance_processed_histories.*.refund_amount' => 'nullable|numeric',
            'contract_insurance_processed_histories.*.refunded_date' => 'nullable|date',
            'contract_insurance_processed_histories.*.is_deleted' => 'nullable|boolean',
        ];
    }
}
