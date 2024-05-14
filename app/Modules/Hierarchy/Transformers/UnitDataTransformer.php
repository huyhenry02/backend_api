<?php

namespace App\Modules\Hierarchy\Transformers;

use App\Modules\MasterData\UnitLevel\Models\UnitLevel;
use App\Modules\Hierarchy\Models\Hierarchy;
use App\Modules\MasterData\MasterDataTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class UnitDataTransformer extends TransformerAbstract
{
    public function transform(Hierarchy $hierarchy)
    {
        $item = [
            'id'       => $hierarchy->id,
            'name'     => $hierarchy->name,
            'unit_code' => $hierarchy->unit_code,
            'establishment_date' => Carbon::parse($hierarchy->establishment_date)->format('d/m/Y'),
            'status' => $hierarchy->status,
            'parent_id' => $hierarchy->parent_id,
            'unit_level' => fractal($hierarchy->unitLevel, new MasterDataTransformer(), new ArraySerializer())->toArray(),
            'created_at' => Carbon::parse($hierarchy->created_at)->format('d/m/Y'),
        ];
        if (isset($hierarchy->unitLevel) && $hierarchy->unitLevel->code == UnitLevel::COMPANY_TYPE) {
            $this->addCompanyDetails($item, $hierarchy);
            $item['is_company'] = true;
        } else {
            $item['mandates'] = $hierarchy->mandates;
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
            $item[$detail] = $hierarchy->$detail;
        }
    }
}
