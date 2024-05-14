<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Employee;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use App\Modules\User\Models\User;
use League\Fractal\TransformerAbstract;

class UserInfoTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->id ?? '',
            'employee_id' => $user->employee_id ?? '',
            'username' => $user->username ?? '',
            'status' => $user->status ?? '',
            'name' => $user->employee->curriculumVitae->name ?? '',
            'email' => $user->employee->curriculumVitae->email ?? '',
            'role' => $user->roles[0]->description ?? '',
            'role_id' => $user->roles[0]->id ?? '',
            'position' => $user->employee->curriculumVitae->position->name ?? '',
            'avatar' => $user->employee->curriculumVitae->getMedia(RawMediaUploadCollectionConstants::FACE_IMAGE) ?? '',
            'phone_number' => $user->employee->curriculumVitae->phone_number ?? '',
            'created_at' => $user->created_at ?? '',
        ];
    }
}
