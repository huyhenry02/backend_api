<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\ContractAllowance;
use App\Modules\MasterData\MasterDataTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ContractAllowanceTransformer extends TransformerAbstract
{
    /**
     * @param ContractAllowance $contractAllowance
     *
     * @return array
     */
    public function transform(ContractAllowance $contractAllowance)
    {
        return [
            'id' => $contractAllowance->id,
            'benefit' => $contractAllowance->benefit,
            'allowance' => fractal($contractAllowance->allowance, new MasterDataTransformer(), new ArraySerializer())->toArray()
        ];
    }
}
