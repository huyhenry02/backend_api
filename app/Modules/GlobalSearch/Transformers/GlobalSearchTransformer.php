<?php

namespace App\Modules\GlobalSearch\Transformers;

use League\Fractal\TransformerAbstract;

class GlobalSearchTransformer extends TransformerAbstract
{


    public function transform($searchData): array
    {
        $dataValue = $searchData->searchable;
        $transformData = [
            'id' => $dataValue->id ?? '',
            'name' => $dataValue->name ?? '',
            'code' => $dataValue->code ?? '',
            'created_at' => $dataValue->created_date ?: ($dataValue->created_at ?: ''),

        ];
        if ($searchData->type === 'employees') {
            $transformData['name'] = $dataValue->curriculumVitae->name ?? '';
        }
        return $transformData;
    }
}
