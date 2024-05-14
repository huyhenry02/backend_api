<?php

namespace App\Modules\Employee\Transformers;

use League\Fractal\TransformerAbstract;

class DataCompareTransformer extends TransformerAbstract
{
    /**
     * @param $data
     * @return array
     */
    public function transform($data): array
    {
        $changes = $data->changedAttributes;
        $changes['working_histories'] = $data->working_history_changes;
        unset($data['working_history_changes']);
        $data['working_histories'] = $data->workingHistories;
        return [
            'oldData' => $data,
            'changed' => $changes
        ];
    }
}
