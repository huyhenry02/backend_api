<?php

namespace App\Modules\Hierarchy\Requests;

use App\Enums\CommonStatusEnum;
use App\Http\Requests\CommonRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateUnitRequest extends CommonRequest
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
        $rules['id'] = 'required|uuid|exists:hierarchies,id';
        $rules['establishment_date'] = 'date';
        $rules['status'] = 'required|in:' . implode(",", CommonStatusEnum::values());

        if ($this->has('is_company')) {
            $rules['is_company'] = "accepted";
            $rules['parent_id'] = "uuid|exists:hierarchies,id";
            $rules['tax_code'] = "string|max:63";
            $rules['address'] = "string|max:63";
            $rules['registration_number'] = "string|max:63";
            $rules['date_of_issue'] = "date";
            $rules['place_of_issue'] = "string|max:127";
            $rules['representative'] = "string|max:63";
            $rules['position'] = "string|max:63";
        } else {
            $rules['level_id'] = 'uuid|exists:unit_levels,id';
            $rules['parent_id'] = 'uuid|exists:hierarchies,id';
            $rules['mandates'] = 'string';
        }
        return $rules;
    }
}
