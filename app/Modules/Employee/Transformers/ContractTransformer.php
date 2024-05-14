<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Contract;
use App\Modules\Hierarchy\Transformers\UnitMinimizationDataTransformer;
use App\Modules\MasterData\MasterDataTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ContractTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     */
    public array $availableIncludes = [
        'contract_type',
        'department',
        'position',
        'employment',
    ];

    public array $defaultIncludes = [
        'contract_working_histories',
        'contract_insurance_processed_histories',
        'contract_allowances',
    ];

    public array $mediaFields = [
        'contract_files',
        'contract_health_records',
    ];

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
            'employee_id' => $contract->employee_id,
            'contract_type_id' => $contract->contract_type_id,
            'department_id' => $contract->department_id,
            'position_id' => $contract->position_id,
            'function' => $contract->function,
            'rank' => $contract->rank,
            'skill_coefficient' => $contract->skill_coefficient,
            'workplace' => $contract->workplace,
            'employment_type_id' => $contract->employment_type_id,
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
        ];

        foreach ($this->mediaFields as $mediaField) {
            $media = $contract->getMedia($mediaField);
            if (count($media) > 0) {
                $transformData[$mediaField] = $media;
            } else {
                $transformData[$mediaField] = null;
            }
        }
        return $transformData;
    }

    public function includeContractType(Contract $contract): ?Item
    {
        return $contract->contractType
            ? $this->item($contract->contractType, new MasterDataTransformer)
            : null;
    }

    public function includePosition(Contract $contract): ?Item
    {
        return $contract->position
            ? $this->item($contract->position, new MasterDataTransformer)
            : null;
    }

    public function includeEmployment(Contract $contract): ?Item
    {
        return $contract->employment
            ? $this->item($contract->employment, new MasterDataTransformer)
            : null;
    }

    public function includeDepartment(Contract $contract): ?Item
    {
        return $contract->department
            ? $this->item($contract->department, new UnitMinimizationDataTransformer)
            : null;
    }

    public function includeContractAllowances(Contract $contract): ?Collection
    {
        return $contract->contractAllowances
            ? $this->collection($contract->contractAllowances, new ContractAllowanceTransformer())
            : null;
    }

    public function includeContractWorkingHistories(Contract $contract): ?Collection
    {
        return $contract->contractWorkingHistories
            ? $this->collection($contract->contractWorkingHistories, new ContractWorkingHistoryTransformer())
            : null;
    }

    public function includeContractInsuranceProcessedHistories(Contract $contract): ?Collection
    {
        return $contract->contractInsuranceProcessedHistories
            ? $this->collection($contract->contractInsuranceProcessedHistories, new ContractInsuranceProcessedHistoryTransformer())
            : null;
    }
}
