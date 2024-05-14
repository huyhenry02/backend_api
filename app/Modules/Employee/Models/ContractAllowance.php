<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;
use App\Modules\MasterData\Allowance\Models\Allowance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractAllowance extends BaseModel
{
    use SoftDeletes;
    protected $table = 'contract_allowances';

    protected $fillable = [
        'contract_id',
        'allowance_id',
        'benefit',
        'status'
    ];

    public function allowance(): BelongsTo
    {
        return $this->belongsTo(Allowance::class, 'allowance_id', 'id');
    }
}
