<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Contract;
use App\Modules\Hierarchy\Transformers\UnitMinimizationDataTransformer;
use App\Modules\MasterData\MasterDataTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ContractDetailTransformer extends TransformerAbstract
{
    /**
     * @param Contract $contract
     *
     * @return array
     */
    public function transform(Contract $contract): array
    {
        $transformData = [
            'id' => $contract->id,
            'code' => $contract->code,
            'contract_type' => fractal($contract->contractType, new MasterDataTransformer(), new ArraySerializer())->toArray(),
            'department' => fractal($contract->department, new UnitMinimizationDataTransformer(), new ArraySerializer())->toArray(),
            'position' => fractal($contract->position, new MasterDataTransformer(), new ArraySerializer())->toArray(),
            'function' => $contract->function,
            'rank' => $contract->rank,
            'skill_coefficient' => $contract->skill_coefficient,
            'workplace' => $contract->workplace,
            'employment' => fractal($contract->employment, new MasterDataTransformer(), new ArraySerializer())->toArray(),
            'effective_date' => $contract->effective_date,
            'signed_date' => $contract->signed_date,
            'signer' => $contract->signer,
            'digital_signature' => $contract->digital_signature,
            'apply_from_date' => $contract->apply_from_date,
            'note' => $contract->note,
            'payment_type' => $contract->payment_type,
            'salary' => $contract->salary,
            'insurance_book_number' => $contract->insurance_book_number,
            'insurance_book_status' => $contract->insurance_book_status,
            'insurers' => $contract->insurers,
            'insurance_card_number' => $contract->insurance_card_number,
            'insurance_city_code' => $contract->insurance_city_code,
            'medical_examination_place' => $contract->medical_examination_place,
            'card_received_date' => $contract->card_received_date,
            'card_returned_date' => $contract->card_returned_date,
            'contract_files' => $contract->contract_files,
            'contract_health_records' => $contract->contract_health_records,
        ];
        if (!empty($contract->contractAllowances)) {
            foreach ($contract->contractAllowances as $contractAllowance) {
                $transformData['contract_allowances'][] = fractal($contractAllowance, new ContractAllowanceTransformer(), new ArraySerializer())->toArray();
            }
        }

        if (!empty($contract->contractWorkingHistories)) {
            foreach ($contract->contractWorkingHistories as $contractWorkingHistory) {
                $transformData['contract_working_histories'][] = fractal($contractWorkingHistory, new ContractWorkingHistoryTransformer(), new ArraySerializer())->toArray();
            }
        }
        return $transformData;
    }
}
