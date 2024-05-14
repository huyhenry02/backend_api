<?php

namespace App\Modules\Employee\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface ContractInterface extends RepositoryInterface
{
    public function getContractDetail($employeeId);
}
