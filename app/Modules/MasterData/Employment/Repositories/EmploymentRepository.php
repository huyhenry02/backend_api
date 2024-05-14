<?php

namespace App\Modules\MasterData\Employment\Repositories;

use App\Modules\MasterData\Employment\Models\Employment;
use App\Modules\MasterData\Employment\Repositories\Interfaces\EmploymentInterface;
use App\Repositories\BaseRepository;

class EmploymentRepository extends BaseRepository implements EmploymentInterface
{
    public function getModel(): string
    {
        return Employment::class;
    }
}
