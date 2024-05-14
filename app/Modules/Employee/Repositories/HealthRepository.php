<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\Health;
use App\Modules\Employee\Repositories\Interfaces\HealthInterface;
use App\Repositories\BaseRepository;

class HealthRepository extends BaseRepository implements HealthInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Health::class;
    }

    public function getChanges(string $id, array $logData): mixed
    {
        $data = $this->getFirstRow(['employee_id' => $id]);
        $data->fill($logData);
        return $data;
    }

}
