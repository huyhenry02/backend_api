<?php

namespace App\Modules\RawMediaUpload\Requests;

use App\Http\Requests\CommonRequest;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class RawMediaUploadRequest extends CommonRequest
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
            'collection' => ['required', 'string', Rule::in(RawMediaUploadCollectionConstants::getAllValues())],
            'files.*' => 'required|mimes:jpeg,png,doc,docs,pdf',
        ];
    }
}
