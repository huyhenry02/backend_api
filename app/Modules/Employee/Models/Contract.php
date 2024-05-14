<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;
use App\Models\MasterModels\ContractType;
use App\Modules\Hierarchy\Models\Hierarchy;
use App\Modules\MasterData\Employment\Models\Employment;
use App\Modules\MasterData\Position\Models\Position;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends BaseModel
{
    use SoftDeletes;
    protected $table = 'contracts';

    protected $fillable = [
        'employee_id',
        'code',
        'contract_type_id',
        'department_id',
        'position_id',
        'function',
        'rank',
        'skill_coefficient',
        'workplace',
        'employment_type_id',
        'effective_date',
        'signed_date',
        'signer',
        'digital_signature',
        'apply_from_date',
        'note',
        'payment_type',
        'salary',
        'insurance_book_number',
        'insurance_book_status',
        'insurers',
        'insurance_card_number',
        'insurance_city_code',
        'medical_examination_place',
        'card_received_date',
        'card_returned_date',
        'status'
    ];

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Hierarchy::class, 'department_id', 'id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'employment_type_id', 'id');
    }

    public function contractAllowances(): HasMany
    {
        return $this->hasMany(ContractAllowance::class, 'contract_id', 'id');
    }

    public function contractWorkingHistories(): HasMany
    {
        return $this->hasMany(ContractWorkingHistory::class, 'contract_id', 'id');
    }

    public function contractInsuranceProcessedHistories(): HasMany
    {
        return $this->hasMany(ContractInsuranceProcessedHistory::class, 'contract_id', 'id');
    }
}
