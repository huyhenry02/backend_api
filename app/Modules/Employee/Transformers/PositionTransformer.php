<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Employee;
use App\Modules\MasterData\Position\Models\Position;
use League\Fractal\TransformerAbstract;

class PositionTransformer extends TransformerAbstract
{

    public array $availableIncludes = [
        'position',
    ];
    /**
     * @param $employee
     * @return array
     */
    public function transform(Position $position): array
    {
        return [
            'id' => $position->id ?? '',
            'name' => $position->name ?? '',
            'code' => $position->code ?? '',
            'status' => $position->status ?? '',
        ];
    }
}
