<?php

namespace App\Modules\MasterData\UnitLevel\Repositories;

use App\Modules\MasterData\UnitLevel\Models\UnitLevel;
use App\Modules\MasterData\UnitLevel\Repositories\Interfaces\UnitLevelInterface;
use App\Repositories\BaseRepository;

class UnitLevelRepository extends BaseRepository implements UnitLevelInterface
{
    public function getModel(): string
    {
        return UnitLevel::class;
    }
}
