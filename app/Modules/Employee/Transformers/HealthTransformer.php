<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Health;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use League\Fractal\TransformerAbstract;

class HealthTransformer extends TransformerAbstract
{
    public function transform(Health $health)
    {
        $healthRecords = $health->getMedia(RawMediaUploadCollectionConstants::HEALTH_RECORDS);
        if (count($healthRecords) < 1) {
            $healthRecords = null;
        }
        return [
            'id' => $health->id,
            'blood_pressure' => $health->blood_pressure,
            'heartbeat' => $health->heartbeat,
            'height' => $health->height,
            'weight' => $health->weight,
            'blood_group' =>$health->blood_group,
            'note' => $health->note,
            'health_records' => $healthRecords
        ];
    }
}
