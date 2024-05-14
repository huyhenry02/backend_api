<?php

namespace App\Modules\RolePermission\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface ModuleInterface extends RepositoryInterface
{
    public function getAllModules($roleId);
}
