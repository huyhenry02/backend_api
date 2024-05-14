<?php

namespace App\Modules\Hierarchy\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface HierarchyInterface extends RepositoryInterface
{
    public function getListUnits();
    public function getUnitDetail($id, array $columns = ['*']);
    public function getUnitDetailWithoutChildUnits($id, array $columns = ['*']);
}
