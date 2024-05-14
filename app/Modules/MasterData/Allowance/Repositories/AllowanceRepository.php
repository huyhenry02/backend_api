<?php

namespace App\Modules\MasterData\Allowance\Repositories;

use App\Modules\MasterData\Allowance\Models\Allowance;
use App\Modules\MasterData\Allowance\Repositories\Interfaces\AllowanceInterface;
use App\Repositories\BaseRepository;

class AllowanceRepository extends BaseRepository implements AllowanceInterface
{
    public function getModel(): string
    {
        return Allowance::class;
    }
}
