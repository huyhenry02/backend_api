<?php

namespace App\Modules\Hierarchy\Transformers;

use App\Modules\Hierarchy\Models\Hierarchy;
use League\Fractal\TransformerAbstract;

class ListUnitTransformer extends TransformerAbstract
{
    public function transform(Hierarchy $hierarchy)
    {
        $item = [
            'id'       => $hierarchy->id,
            'name'     => $hierarchy->name
        ];
        if (isset($hierarchy['childUnits'])) {
            foreach ($hierarchy->childUnits as $childUnit) {
                $item['child_units'][] = $this->transform($childUnit);
            }
        }
        return $item;
    }
}
