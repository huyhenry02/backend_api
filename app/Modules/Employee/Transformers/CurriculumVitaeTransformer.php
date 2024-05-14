<?php

namespace App\Modules\Employee\Transformers;

use App\Modules\Employee\Models\CurriculumVitae;
use App\Modules\MasterData\MasterDataTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CurriculumVitaeTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     */
    public array $availableIncludes = [
        'working_histories',
        'employee',
        'leader',
        'position',
        'subsidiary',
        'nationality',
    ];

    public array $mediaFields = [
        'identification_front',
        'identification_back',
        'face_image',
        'fingerprint'
    ];

    /**
     * @param CurriculumVitae $curriculumVitae
     *
     * @return array
     */
    public function transform(CurriculumVitae $curriculumVitae): array
    {
        $data = $curriculumVitae->toArray();
        foreach ($this->mediaFields as $mediaField) {
            $media = $curriculumVitae->getMedia($mediaField);
            if (count($media) > 0) {
                $data[$mediaField] = $media;
            } else {
                $data[$mediaField] = null;
            }
        }
        return $data;
    }

    public function includeWorkingHistories(CurriculumVitae $curriculumVitae): ?Collection
    {
        return $curriculumVitae->workingHistories
            ? $this->collection($curriculumVitae->workingHistories, new WorkingHistoryTransformer())
            : null;
    }

    public function includeEmployee(CurriculumVitae $curriculumVitae): ?Item
    {
        return $curriculumVitae->employee
            ? $this->item($curriculumVitae->employee, new EmployeeTransformer)
            : null;
    }

    public function includeLeader(CurriculumVitae $curriculumVitae): ?Item
    {
        return $curriculumVitae->leader
            ? $this->item($curriculumVitae->leader, new EmployeeTransformer)
            : null;
    }

    public function includePosition(CurriculumVitae $curriculumVitae): ?Item
    {
        return $curriculumVitae->position
            ? $this->item($curriculumVitae->position, new MasterDataTransformer())
            : null;
    }

    public function includeSubsidiary(CurriculumVitae $curriculumVitae): ?Item
    {
        return $curriculumVitae->subsidiary
            ? $this->item($curriculumVitae->subsidiary, new SubsidiaryTransformer)
            : null;
    }

    public function includeNationality(CurriculumVitae $curriculumVitae): ?Item
    {
        return $curriculumVitae->nationality
            ? $this->item($curriculumVitae->nationality, new MasterDataTransformer())
            : null;
    }
}
