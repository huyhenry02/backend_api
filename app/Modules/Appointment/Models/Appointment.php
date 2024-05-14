<?php

namespace App\Modules\Appointment\Models;

use App\Models\BaseModel;
use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\WorkingHistory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends BaseModel
{
    use SoftDeletes;

    public $table = 'appointments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        "name",
        "employee_id",
        "registerer_id",
        "email",
        "start_time",
        "end_time",
        "phone",
        "identification",
        "reason",
        "reject_reason",
        "rejected_by",
        "status",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function register(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'registerer_id');
    }
}
