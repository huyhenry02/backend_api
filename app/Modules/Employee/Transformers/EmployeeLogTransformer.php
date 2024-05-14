<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\EmployeeLog;
use League\Fractal\TransformerAbstract;

class EmployeeLogTransformer extends TransformerAbstract
{
    /**
     * @param Employee $employeeLog
     * @return array
     */
    public function transform(EmployeeLog $employeeLog): array
    {
        return [
            'id' => $employeeLog->id,
            'data' => $employeeLog->data,
        ];
    }
}
