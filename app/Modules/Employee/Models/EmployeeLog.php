<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;

class EmployeeLog extends BaseModel
{

    public $table = 'employee_logs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'employee_id',
        'type',
        'data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];
}
