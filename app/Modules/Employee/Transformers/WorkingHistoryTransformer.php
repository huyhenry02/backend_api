<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\CurriculumVitae;
use App\Modules\Employee\Models\WorkingHistory;
use League\Fractal\TransformerAbstract;

class WorkingHistoryTransformer extends TransformerAbstract
{
    /**
     * @param $workingHistory
     *
     * @return array
     */
    public function transform($workingHistory): array
    {
        return $workingHistory->toArray();
    }
}
