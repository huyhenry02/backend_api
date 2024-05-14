<?php

namespace App\Modules\MasterData\InsurancePolicy\Repositories;

use App\Modules\MasterData\InsurancePolicy\Models\InsurancePolicy;
use App\Modules\MasterData\InsurancePolicy\Repositories\Interfaces\InsurancePolicyInterface;
use App\Repositories\BaseRepository;

class InsurancePolicyRepository extends BaseRepository implements InsurancePolicyInterface
{
    public function getModel(): string
    {
        return InsurancePolicy::class;
    }
}
