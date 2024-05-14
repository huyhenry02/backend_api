<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\CurriculumVitae;
use App\Modules\Employee\Repositories\Interfaces\CurriculumVitaeInterface;
use App\Repositories\BaseRepository;
use JsonException;

class CurriculumVitaeRepository extends BaseRepository implements CurriculumVitaeInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return CurriculumVitae::class;
    }

    /**
     * @param string $id
     * @param array  $logData
     *
     * @return mixed
     */
    public function getChanges(string $id, array $logData): mixed
    {
        $data = $this->getFirstRow(['employee_id' => $id]);
        $data->fill($logData);
        return $data;
    }
}
