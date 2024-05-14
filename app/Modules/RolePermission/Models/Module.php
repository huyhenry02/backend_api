<?php

namespace App\Modules\RolePermission\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Module extends BaseModel
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id', 'id');
    }
}
