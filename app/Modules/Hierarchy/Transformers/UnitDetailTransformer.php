<?php

namespace App\Modules\Hierarchy\Transformers;

use App\Modules\MasterData\UnitLevel\Models\UnitLevel;
use App\Modules\Hierarchy\Models\Hierarchy;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UnitDetailTransformer extends TransformerAbstract
{
    public function transform(Hierarchy $hierarchy)
    {
        $item = [
            'id'       => $hierarchy->id,
            'name'     => $hierarchy->name,
            'unit_code' => $hierarchy->unit_code,
            'parent_id' => $hierarchy->parent_id,
            'establishment_date' => Carbon::parse($hierarchy->establishment_date)->format('d/m/Y'),
            'status' => $hierarchy->status,
            'unit_level' => [
                'id' => $hierarchy->unitLevel->id,
                'name' => $hierarchy->unitLevel->name,
                'code' => $hierarchy->unitLevel->code
            ],
            'created_at' => Carbon::parse($hierarchy->created_at)->format('d/m/Y'),
        ];

        if (isset($hierarchy->unitLevel) && $hierarchy->unitLevel->code == UnitLevel::COMPANY_TYPE) {
            $this->addCompanyDetails($item, $hierarchy);
            $item['is_company'] = true;
        } else {
            $item['mandates'] = $hierarchy->mandates;
        }

        if (!empty($hierarchy->unitDetail)) {
            foreach ($hierarchy->unitDetail as $childUnit) {
                $item['child_units'][] = $this->transform($childUnit);
            }
        }
        return $item;
    }

    private function addCompanyDetails(&$item, $hierarchy)
    {
        $companyDetails = [
            'tax_code', 'address', 'registration_number',
            'date_of_issue', 'place_of_issue',
            'representative', 'position',
        ];

        foreach ($companyDetails as $detail) {
            if ($detail === 'date_of_issue') {
                $item[$detail] = Carbon::parse($hierarchy->$detail)->format('d/m/Y');
                continue;
            }
            $item[$detail] = $hierarchy->$detail;
        }
    }
}
