<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHistory extends BaseModel
{
    use SoftDeletes;

    public $table = 'working_histories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'curriculum_vitae_id',
        'start_date',
        'end_date',
        'position',
        'company',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'curriculum_vitae_id',
        'created_at',
        'updated_at',
    ];
}
