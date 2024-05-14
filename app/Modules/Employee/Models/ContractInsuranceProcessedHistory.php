<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;
use App\Modules\MasterData\Allowance\Models\Allowance;
use App\Modules\MasterData\InsurancePolicy\Models\InsurancePolicy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractInsuranceProcessedHistory extends BaseModel
{
    protected $table = 'contract_insurance_processed_histories';

    protected $fillable = [
        'contract_id',
        'insurance_policy_id',
        'received_date',
        'completed_date',
        'refund_amount',
        'refunded_date',
    ];

    /**
     * @return BelongsTo
     */
    public function insurancePolicy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class);
    }
}
