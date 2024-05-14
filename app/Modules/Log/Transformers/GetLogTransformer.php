<?php

namespace App\Modules\Log\Transformers;

use App\Modules\Employee\Transformers\EmployeeTransformer;
use App\Modules\Log\Model\Log;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class GetLogTransformer extends TransformerAbstract
{
    public function transform(Log $log): array
    {
        $newData = json_decode($log->new_data, true);
        $oldData = json_decode($log->old_data, true);
        return [
            'id' => $log->id,
            'event' => $log->action,
            'old_data' => $oldData,
            'new_data' => $newData,
            'employee_id' => $log->employee_id,
            'employee' => fractal($log->employee, new EmployeeTransformer(), new ArraySerializer()),
            'created_at' => $log->created_at,
        ];
    }

}
