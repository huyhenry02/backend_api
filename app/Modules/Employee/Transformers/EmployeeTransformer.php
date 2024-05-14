<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Employee;
use League\Fractal\TransformerAbstract;

class EmployeeTransformer extends TransformerAbstract
{

    public array $availableIncludes = [
        'workingHistories',
        'curriculumVitae'
    ];
    /**
     * @param $employee
     * @return array
     */
    public function transform(Employee $employee): array
    {
        return [
            'id' => $employee->id ?? '',
            'code' => $employee->code ?? '',
            'email' => $employee->curriculumVitae->email ?? '',
            'name' => $employee->curriculumVitae->name ?? '',
            'position' => $employee->curriculumVitae->position->name ?? '',
            'status' => $employee->status ?? '',
            'created_at' => $employee->curriculumVitae->created_at ?? '',
            'account_created_at' => $employee->user->created_at ?? '',
        ];
    }
}
