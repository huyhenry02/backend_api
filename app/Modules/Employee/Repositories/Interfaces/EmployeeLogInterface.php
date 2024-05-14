<?php

namespace App\Modules\Employee\Repositories\Interfaces;

use App\Enums\EmployeeRecordTypeEnum;
use App\Repositories\Interfaces\RepositoryInterface;

interface EmployeeLogInterface extends RepositoryInterface
{
    /**
     * Create record log when update curriculum vitae
     *
     * @param array                  $data
     * @param EmployeeRecordTypeEnum $type
     *
     * @return void
     */
    public function createLog(array $data, EmployeeRecordTypeEnum $type = EmployeeRecordTypeEnum::CV): void;

    public function getAllLogs(mixed $employeeId, EmployeeRecordTypeEnum $type = EmployeeRecordTypeEnum::CV,  int $perPage = DEFAULT_RECORDS_PER_PAGE);

    public function getDataLogs(mixed $uuid);
}
