<?php

namespace App\Modules\MasterData\SalaryType\Repositories;

use App\Modules\MasterData\SalaryType\Models\SalaryType;
use App\Modules\MasterData\SalaryType\Repositories\Interfaces\SalaryTypeInterface;
use App\Repositories\BaseRepository;

class SalaryTypeRepository extends BaseRepository implements SalaryTypeInterface
{
    public function getModel(): string
    {
        return SalaryType::class;
    }
}
