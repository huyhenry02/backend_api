<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\contractInsuranceProcessedHistory;
use App\Modules\Hierarchy\Transformers\UnitMinimizationDataTransformer;
use App\Modules\MasterData\MasterDataTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ContractInsuranceProcessedHistoryTransformer extends TransformerAbstract
{
//    public array $defaultIncludes = [
//        'insurance_policy',
//    ];

    /**
     * @param ContractInsuranceProcessedHistory $contractInsuranceProcessedHistory
     *
     * @return array
     */
    public function transform(ContractInsuranceProcessedHistory $contractInsuranceProcessedHistory)
    {
        $transformData =  [
            'id' => $contractInsuranceProcessedHistory->id,
            'insurance_policy_id' => $contractInsuranceProcessedHistory->insurance_policy_id,
            'received_date' => $contractInsuranceProcessedHistory->received_date,
            'completed_date' => $contractInsuranceProcessedHistory->completed_date,
            'refund_amount' => $contractInsuranceProcessedHistory->refund_amount,
            'refunded_date' => $contractInsuranceProcessedHistory->refunded_date,
        ];

        if (!empty($contractInsuranceProcessedHistory->insurancePolicy)) {
            $transformData['insurance_policy'] = fractal($contractInsuranceProcessedHistory->insurancePolicy, new MasterDataTransformer(), new ArraySerializer())
                ->toArray();
        }

        return $transformData;
    }

//    public function includeInsurancePolicy(ContractInsuranceProcessedHistory $contractInsuranceProcessedHistory): ?Item
//    {
//        return $contractInsuranceProcessedHistory->insurancePolicy
//            ? $this->item($contractInsuranceProcessedHistory->insurancePolicy, new MasterDataTransformer())
//            : null;
//    }
}
