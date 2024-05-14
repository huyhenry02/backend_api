<?php

namespace App\Modules\MasterData\Religion\Repositories;

use App\Modules\MasterData\Religion\Models\Religion;
use App\Modules\MasterData\Religion\Repositories\Interfaces\ReligionInterface;
use App\Repositories\BaseRepository;

class ReligionRepository extends BaseRepository implements ReligionInterface
{
    public function getModel(): string
    {
        return Religion::class;
    }
}
