<?php

namespace App\Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission implements Auditable
{
    use HasUuids, SoftDeletes, \OwenIt\Auditing\Auditable;
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'guard_name',
        'module_id',
    ];

    public function modelModule(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }
}
