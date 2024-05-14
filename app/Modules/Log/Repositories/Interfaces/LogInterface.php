<?php

namespace App\Modules\Log\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface LogInterface extends RepositoryInterface
{
    public static function createLog(string $id, string $model_type, string $action, string $employeeId, $newData = '{}', $oldData = '{}');

    public function getLogData(string $model_type, string $id);
}
