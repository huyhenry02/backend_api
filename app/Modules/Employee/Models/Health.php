<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;

class Health extends BaseModel
{


    public $table = 'healths';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'blood_pressure',
        'heartbeat',
        'height',
        'weight',
        'blood_group',
        'note',
        'employee_id',
        'code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];
}
