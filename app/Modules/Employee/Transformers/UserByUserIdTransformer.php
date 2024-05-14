<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Employee;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use App\Modules\User\Models\User;
use League\Fractal\TransformerAbstract;

class UserByUserIdTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user): array
    {
        $cv = $user->employee->curriculumVitae;
        return [
            'id' => $user->id ?? '',
            'username' => $user->username ?? '',
            'employee_code' => $user->employee->code ?? '',
            'status' => $user->status ?? '',
            'name' => $cv->name ?? '',
            'email' => $cv->email ?? '',
            'gender' => $cv->gender ?? '',
            'dob' => $cv->dob ? date('d/m/Y', strtotime($cv->dob)) : '',
            'created_at' => $user->created_at ? date('d/m/Y H:i', strtotime($user->created_at)) : '',
            'role' => $user->roles[0]->description ?? '',
            'position' => $cv->position->name ?? '',
            'subsidiary' => $cv->subsidiary->name ?? '',
            'avatar' => $cv->getFirstMediaUrl(RawMediaUploadCollectionConstants::FACE_IMAGE) ?? '',
            'phone_number' => $cv->phone_number ?? '',
        ];
    }
}
