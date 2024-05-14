<?php

namespace App\Modules\RolePermission\Repositories;

use App\Modules\RolePermission\Models\Permission;
use App\Modules\RolePermission\Models\Role;
use App\Modules\RolePermission\Repositories\Interfaces\PermissionInterface;
use App\Repositories\BaseRepository;

class PermissionRepository extends BaseRepository implements PermissionInterface
{
    public function getModel(): string
    {
        return Permission::class;
    }

    public function getAllPermissions(int $recordsPerPage, $checkSoftDelete = false)
    {
        $selectColumns = [
            'id',
            'name',
            'status',
            'description',
            'module_id',
        ];
        $query = Permission::select($selectColumns);
        if ($checkSoftDelete) {
            $query = $query->withTrashed();
        }

        if ($recordsPerPage != GET_ALL_ITEMS)
        {
            return $query->paginate($recordsPerPage);
        }
        return $query->get();

    }

}
