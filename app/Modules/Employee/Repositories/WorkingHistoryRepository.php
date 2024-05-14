<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\WorkingHistory;
use App\Modules\Employee\Repositories\Interfaces\EmployeeInterface;
use App\Modules\Employee\Repositories\Interfaces\WorkingHistoryInterface;
use App\Repositories\BaseRepository;

class WorkingHistoryRepository extends BaseRepository implements WorkingHistoryInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return WorkingHistory::class;
    }

    /**
     * @param string $id
     * @param array  $workingHistory
     *
     * @return mixed
     */
    public function updateWorkingHistories(string $id, array $workingHistory): mixed
    {
        $history = $this->find($id);
        $history->fill($workingHistory);
        return $history->save();
    }
}
