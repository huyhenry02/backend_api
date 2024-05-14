<?php

namespace App\Modules\Hierarchy\Transformers;

use App\Modules\Hierarchy\Models\Hierarchy;
use League\Fractal\TransformerAbstract;

class UnitMinimizationDataTransformer extends TransformerAbstract
{
    /**
     * @param Hierarchy $hierarchy
     *
     * @return array
     */
    public function transform(Hierarchy $hierarchy): array
    {
        return [
            'id'       => $hierarchy->id,
            'name'     => $hierarchy->name
        ];
    }
}
