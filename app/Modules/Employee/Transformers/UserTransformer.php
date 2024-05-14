<?php

namespace App\Modules\Employee\Transformers;


use App\Modules\User\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'username' => $user->username ?? '',
            'name' => $user->employee->curriculumVitae->name ?? '',
            'email' => $user->employee->curriculumVitae->email ?? '',
            'role' => $user->roles[0]->description ?? '',
        ];
    }
}
