<?php

namespace App\Modules\Employee\Repositories;

use App\Enums\EmployeeRecordTypeEnum;
use App\Modules\Employee\Models\EmployeeLog;
use App\Modules\Employee\Repositories\Interfaces\EmployeeLogInterface;
use App\Repositories\BaseRepository;
use JsonException;

class EmployeeLogRepository extends BaseRepository implements EmployeeLogInterface
{
    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return EmployeeLog::class;
    }

    /**
     * @param array                  $data
     * @param EmployeeRecordTypeEnum $type
     * @return void
     */
    public function createLog(array $data, EmployeeRecordTypeEnum $type = EmployeeRecordTypeEnum::CV): void
    {
        $this->create([
            'employee_id' => $data['employee_id'],
            'type' => $type,
            'data' => $data['data'],
        ]);
    }
    /**
     * @param mixed                  $employeeId
     * @param EmployeeRecordTypeEnum $type
     * @param int                    $perPage
     *
     * @return mixed
     */
    public function getAllLogs(mixed $employeeId, EmployeeRecordTypeEnum $type = EmployeeRecordTypeEnum::CV, int $perPage = DEFAULT_RECORDS_PER_PAGE): mixed
    {
        return $this->_model
            ->where([
                ['employee_id', $employeeId],
                ['type', $type]
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * @param mixed $uuid
     * @return mixed
     * @throws JsonException
     */
    public function getDataLogs(mixed $uuid): mixed
    {
        return json_decode($this->find($uuid)->data, true, 512, JSON_THROW_ON_ERROR);
    }
}
