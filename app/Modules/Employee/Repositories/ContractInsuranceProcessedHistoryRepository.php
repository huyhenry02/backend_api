<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\ContractInsuranceProcessedHistory;
use App\Modules\Employee\Repositories\Interfaces\ContractInsuranceProcessedHistoryInterface;
use App\Repositories\BaseRepository;

class ContractInsuranceProcessedHistoryRepository extends BaseRepository implements ContractInsuranceProcessedHistoryInterface
{
    public function getModel(): string
    {
        return ContractInsuranceProcessedHistory::class;
    }
}
