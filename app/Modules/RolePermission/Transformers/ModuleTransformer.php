<?php

namespace App\Modules\RolePermission\Transformers;

use App\Modules\RolePermission\Models\Module;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class ModuleTransformer extends TransformerAbstract
{
    public array $defaultIncludes = [
        'permissions',
    ];
    public function transform(Module $module)
    {
        return [
            'description' => $module->description,
            'name' => $module->name,
        ];
    }

    public function includePermissions(Module $module): Collection|null
    {
        return $module->permissions
            ? $this->collection($module->permissions, new PermissionTransformer())
            : null;
    }
}
