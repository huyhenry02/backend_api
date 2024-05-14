<?php

namespace App\Modules\RolePermission\Repositories;

use App\Modules\RolePermission\Models\Role;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class RoleRepository extends BaseRepository implements RoleInterface
{
    public function getModel(): string
    {
        return Role::class;
    }

    public function findByName($roleName, bool $checkSoftDelete = false)
    {
        $selectColumns = [
            'id'
        ];
        $query =  Role::select($selectColumns)->where([
            'name' => $roleName
        ]);
        if ($checkSoftDelete) {
            $query = $query->withTrashed();
        }
        return $query->first();
    }

    public function getAllRoles(int $recordsPerPage, $checkSoftDelete = false)
    {
        $selectColumns = [
            'id',
            'name',
            'description',
            'status',
            'created_at'
        ];
        $query = Role::select($selectColumns);
        if ($checkSoftDelete) {
            $query = $query->withTrashed();
        }
        $query = $query->orderBy('status', 'ASC')->orderBy('description', 'ASC');

        if ($recordsPerPage != GET_ALL_ITEMS)
        {
            return $query->paginate($recordsPerPage);
        }
        return $query->get();

    }

}
