<?php

namespace App\Modules\Hierarchy\Repositories;

use App\Enums\CommonStatusEnum;
use App\Modules\Hierarchy\Models\Hierarchy;
use App\Modules\Hierarchy\Repositories\Interfaces\HierarchyInterface;
use App\Repositories\BaseRepository;

class HierarchyRepository extends BaseRepository implements HierarchyInterface
{
    public function getModel(): string
    {
        return Hierarchy::class;
    }

    public function getListUnits(array $columns = ['*'])
    {
        return Hierarchy::select($columns)
            ->with([
                'childUnits' => function ($query) {
                    return $query->select('id', 'parent_id', 'name');
                }
            ])
            ->where('status', '=', CommonStatusEnum::ACTIVE->value)
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->get();
    }

    public function getUnitDetail($id, array $columns = ['*'])
    {
        return Hierarchy::select($columns)->with(['unitLevel', 'unitDetail'])
            ->where('id','=',$id)
            ->first();
    }

    public function getUnitDetailWithoutChildUnits($id, array $columns = ['*'])
    {
        return Hierarchy::select($columns)->with(['unitLevel:id,name,code,status'])
            ->where('id','=',$id)
            ->first();
    }
}
