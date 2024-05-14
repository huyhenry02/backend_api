<?php

namespace App\Modules\RolePermission\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface RoleInterface extends RepositoryInterface
{
    /**
     * get all roles with pagination
     *
     * @param int $recordsPerPage
     * @param bool $checkSoftDelete
     */
    public function getAllRoles(int $recordsPerPage, bool $checkSoftDelete = false);

    /**
     * find role by name
     *
     * @param $roleName
     * @param bool $checkSoftDelete
     */
    public function findByName($roleName, bool $checkSoftDelete = false);
}
