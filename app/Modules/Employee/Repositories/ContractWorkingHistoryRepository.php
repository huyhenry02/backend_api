<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\ContractWorkingHistory;
use App\Modules\Employee\Repositories\Interfaces\ContractWorkingHistoryInterface;
use App\Repositories\BaseRepository;

class ContractWorkingHistoryRepository extends BaseRepository implements ContractWorkingHistoryInterface
{
    public function getModel(): string
    {
        return ContractWorkingHistory::class;
    }
}
