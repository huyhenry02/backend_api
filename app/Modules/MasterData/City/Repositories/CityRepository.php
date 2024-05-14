<?php

namespace App\Modules\MasterData\City\Repositories;

use App\Modules\MasterData\City\Models\City;
use App\Modules\MasterData\City\Repositories\Interfaces\CityInterface;
use App\Repositories\BaseRepository;

class CityRepository extends BaseRepository implements CityInterface
{
    public function getModel(): string
    {
        return City::class;
    }
}
