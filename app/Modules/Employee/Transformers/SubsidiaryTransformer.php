<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\Employee;
use App\Modules\Hierarchy\Models\Hierarchy;
use App\Modules\MasterData\Position\Models\Position;
use League\Fractal\TransformerAbstract;

class SubsidiaryTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     */
    public array $availableIncludes = [
        'parent',
    ];

    /**
     * @param Hierarchy $subsidiary
     *
     * @return array
     */
    public function transform(Hierarchy $subsidiary): array
    {
        return [
            'id' => $subsidiary->id ?? '',
            'name' => $subsidiary->name ?? '',
            'unit_code' => $subsidiary->unit_code ?? '',
            'tax_code' => $subsidiary->tax_code ?? '',
            'address' => $subsidiary->address ?? '',
            'registration_number' => $subsidiary->registration_number ?? '',
        ];
    }
}
