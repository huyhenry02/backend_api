<?php

namespace App\Modules\Hierarchy\Models;

use App\Enums\CommonStatusEnum;
use App\Models\BaseModel;
use App\Modules\Employee\Models\Contract;
use App\Modules\Employee\Models\ContractWorkingHistory;
use App\Modules\Employee\Models\CurriculumVitae;
use App\Modules\MasterData\UnitLevel\Models\UnitLevel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hierarchy extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "unit_code",
        "parent_id",
        "tax_code",
        "address",
        "establishment_date",
        "registration_number",
        "date_of_issue",
        "place_of_issue",
        "representative",
        "position",
        "level_id",
        "mandates",
        "status"
    ];

    public function childUnits()
    {
        return $this->hasMany(Hierarchy::class, 'parent_id', 'id')->with('childUnits')
            ->where('status', '=', CommonStatusEnum::ACTIVE->value);
    }

    public function unitLevel()
    {
        return $this->belongsTo(UnitLevel::class, 'level_id', 'id');
    }

    public function unitDetail()
    {
        return $this->hasMany(Hierarchy::class, 'parent_id', 'id')->with(['unitLevel', 'unitDetail']);

    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class, 'department_id', 'id');
    }

    public function fromDepartment(): HasMany
    {
        return $this->hasMany(ContractWorkingHistory::class, 'from_department', 'id');
    }

    public function toDepartment(): HasMany
    {
        return $this->hasMany(ContractWorkingHistory::class, 'from_department', 'id');
    }

    public function subsidiary(): HasMany
    {
        return $this->hasMany(CurriculumVitae::class, 'subsidiary_id', 'id');
    }
}
