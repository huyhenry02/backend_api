<?php

namespace App\Modules\RolePermission\Transformers;

use App\Modules\RolePermission\Models\Permission;
use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{
    public function transform(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'description' => $permission->description,
            'in_role' => $permission->inRole,
            'in_module' => $permission->modelModule->name ?? '',
            'in_module_description' => $permission->modelModule->description ?? ''
        ];
    }
}
