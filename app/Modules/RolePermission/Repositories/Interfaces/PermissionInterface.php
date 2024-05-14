<?php

namespace App\Modules\RolePermission\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface PermissionInterface extends RepositoryInterface
{
    /**
     * get all roles with pagination
     *
     * @param int $recordsPerPage
     * @param bool $checkSoftDelete
     */
    public function getAllPermissions(int $recordsPerPage, bool $checkSoftDelete = false);

}
