<?php

namespace App\Modules\Log\Model;

use App\Models\BaseModel;
use App\Modules\Employee\Models\Employee;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends BaseModel
{


    public $table = 'logs';
    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */

    protected $fillable = [
          'employee_id',
          'model_type',
          'model_id',
          'action',
          'old_data',
          'new_data',
    ];
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
