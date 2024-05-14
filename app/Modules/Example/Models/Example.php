<?php

namespace App\Modules\Example\Models;

use App\Models\BaseModel;

class Example extends BaseModel
{


    public $table = 'example';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'des',
    ];
}
