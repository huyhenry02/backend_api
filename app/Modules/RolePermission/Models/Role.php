<?php

namespace App\Modules\RolePermission\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Role extends SpatieRole implements Auditable, Searchable
{
    use HasUuids, SoftDeletes, \OwenIt\Auditing\Auditable;
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'guard_name',
        'status',
        'deleted_at'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });

        self::updating(function ($model) {
            $model->name = Str::slug($model->description);
        });
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'model_has_roles', 'role_id', 'model_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class,'role_has_permissions', 'role_id', 'permission_id');
    }

    public function getSearchResult(): SearchResult
    {
        $type = 'Role';
        return new \Spatie\Searchable\SearchResult(
            $this,
            $type,
        );
    }
}
