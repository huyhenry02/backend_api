<?php

namespace App\Modules\RolePermission\Models;

use App\Models\BaseModel;

class ModelHasRole extends BaseModel
{
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
        });
    }
}
