<?php

namespace App\Modules\RolePermission\Transformers;

use App\Enums\CommonStatusEnum;
use App\Modules\RolePermission\Models\Role;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    public array $defaultIncludes = [
        'permissions',
    ];

    public function transform(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'status' => $role->status,
            'created_at' => $role->created_at
        ];
    }

    public function includePermissions(Role $role): ?Collection
    {
        return $role->permissions
            ? $this->collection($role->permissions, new PermissionTransformer())
            : null;
    }
}
