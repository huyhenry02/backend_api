<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\ContractAllowance;
use App\Modules\Employee\Repositories\Interfaces\ContractAllowanceInterface;
use App\Repositories\BaseRepository;

class ContractAllowanceRepository extends BaseRepository implements ContractAllowanceInterface
{
    public function getModel(): string
    {
        return ContractAllowance::class;
    }
}
