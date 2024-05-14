<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;
use App\Modules\Hierarchy\Models\Hierarchy;
use App\Modules\MasterData\Nationality\Models\Nationality;
use App\Modules\MasterData\Position\Models\Position;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumVitae extends BaseModel
{


    public $table = 'curriculum_vitaes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        "employee_id",
        "code",
        "name",
        "nationality",
        "email",
        "phone_number",
        "dob",
        "gender",
        "country",
        "marital",
        "ethnic",
        "region_id",
        "identification",
        "place_of_issue",
        "date_of_issue",
        "tax_code",
        "onboard_date",
        "leader_id",
        "subsidiary_id",
        "position_id",
        "address",
        "bank_account_number",
        "bank_account_name",
        "bank_name",
        "bank_branch",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at',
    ];

    /**
     * @return array
     */
    public function getChangedAttributesAttribute(): array
    {
        return array_keys($this->getDirty());
    }

    /**
     * @return HasMany
     */
    public function workingHistories(): HasMany
    {
        return $this->hasMany(WorkingHistory::class)->orderBy('created_at');
    }

    /**
     * @return BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'leader_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function subsidiary(): BelongsTo
    {
        return $this->belongsTo(Hierarchy::class, 'subsidiary_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }
}
