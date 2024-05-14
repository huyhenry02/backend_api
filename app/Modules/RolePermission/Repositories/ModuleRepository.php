<?php

namespace App\Modules\RolePermission\Repositories;

use App\Modules\RolePermission\Models\Module;
use App\Modules\RolePermission\Repositories\Interfaces\ModuleInterface;
use App\Repositories\BaseRepository;

class ModuleRepository extends BaseRepository implements ModuleInterface
{
    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Module::class;
    }

    public function getAllModules($roleId)
    {
        $selectColumns = [
            'id',
            'name',
            'description'
        ];
        return $this->_model->select($selectColumns)->with('permissions:id,name,module_id,guard_name,description')->get();
    }
}
