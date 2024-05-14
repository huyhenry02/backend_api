<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\ContractInsuranceProcessedHistory;
use App\Modules\Employee\Models\ContractWorkingHistory;
use App\Modules\Hierarchy\Models\Hierarchy;
use App\Modules\Hierarchy\Transformers\UnitDetailTransformer;
use App\Modules\Hierarchy\Transformers\UnitMinimizationDataTransformer;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ContractWorkingHistoryTransformer extends TransformerAbstract
{
//    public array $defaultIncludes = [
//        'from_department',
//        'to_department',
//    ];

    public array $mediaFields = [
        RawMediaUploadCollectionConstants::JOB_TRANSFER_PROOFS,
    ];

    /**
     * @param ContractWorkingHistory $contractWorkingHistory
     *
     * @return array
     */
    public function transform(ContractWorkingHistory $contractWorkingHistory): array
    {
        $transformData = [
            'id' => $contractWorkingHistory->id,
            'from_department' => $contractWorkingHistory->from_department,
            'to_department' => $contractWorkingHistory->to_department,
            'worked_from_date' => $contractWorkingHistory->worked_from_date,
            'worked_to_date' => $contractWorkingHistory->worked_to_date,
            'reason' => $contractWorkingHistory->reason
        ];

        foreach ($this->mediaFields as $mediaField) {
            $transformData[$mediaField] = $contractWorkingHistory->getMedia($mediaField);
        }
        return $transformData;
    }

    public function includeFromDepartment(ContractWorkingHistory $contractWorkingHistory): ?Item
    {
        return $contractWorkingHistory->fromDepartment
            ? $this->item($contractWorkingHistory->fromDepartment, new UnitMinimizationDataTransformer())
            : null;
    }

    public function includeToDepartment(ContractWorkingHistory $contractWorkingHistory): ?Item
    {
        return $contractWorkingHistory->toDepartment
            ? $this->item($contractWorkingHistory->toDepartment, new UnitMinimizationDataTransformer())
            : null;
    }
}
