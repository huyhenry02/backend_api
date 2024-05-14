<?php

namespace App\Modules\MasterData\ContractType\Repositories;

use App\Modules\MasterData\ContractType\Models\ContractType;
use App\Modules\MasterData\ContractType\Repositories\Interfaces\ContractTypeInterface;
use App\Repositories\BaseRepository;

class ContractTypeRepository extends BaseRepository implements ContractTypeInterface
{
    public function getModel(): string
    {
        return ContractType::class;
    }
}
