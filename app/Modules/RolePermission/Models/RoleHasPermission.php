<?php

namespace App\Modules\RolePermission\Models;

use App\Models\BaseModel;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleHasPermission extends BaseModel
{
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): belongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
