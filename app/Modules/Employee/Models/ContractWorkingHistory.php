<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;
use App\Modules\Hierarchy\Models\Hierarchy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractWorkingHistory extends BaseModel
{
    use SoftDeletes;
    protected $table = 'contract_working_histories';

    protected $fillable = [
        'contract_id',
        'worked_from_date',
        'worked_to_date',
        'from_department',
        'to_department',
        'reason',
        'status'
    ];

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Hierarchy::class, 'from_department', 'id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Hierarchy::class, 'to_department', 'id');
    }
}
