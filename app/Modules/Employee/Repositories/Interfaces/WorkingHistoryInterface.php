<?php

namespace App\Modules\Employee\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface WorkingHistoryInterface extends RepositoryInterface
{
    public function updateWorkingHistories(string $id, array $workingHistory);
}
