<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\Contract;
use App\Modules\Employee\Repositories\Interfaces\ContractInterface;
use App\Repositories\BaseRepository;

class ContractRepository extends BaseRepository implements ContractInterface
{
    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Contract::class;
    }

    public function getContractDetail($employeeId)
    {
        return $this->_model->where('employee_id', '=', $employeeId)
            ->with([
                'contractType:id,name,code',
                'department:id,name',
                'position:id,name,code',
                'employment:id,name,code',
                'contractAllowances:id,contract_id,allowance_id,benefit',
                'contractAllowances.allowance:id,name,code',
                'contractWorkingHistories:id,contract_id,worked_from_date,worked_to_date,from_department,to_department,reason',
                'contractWorkingHistories.fromDepartment:id,name',
                'contractWorkingHistories.toDepartment:id,name',
            ])
            ->first();
    }
}
