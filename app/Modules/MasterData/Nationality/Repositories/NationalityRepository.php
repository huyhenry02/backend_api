<?php

namespace App\Modules\MasterData\Nationality\Repositories;

use App\Modules\MasterData\Nationality\Models\Nationality;
use App\Modules\MasterData\Nationality\Repositories\Interfaces\NationalityInterface;
use App\Repositories\BaseRepository;

class NationalityRepository extends BaseRepository implements NationalityInterface
{
    public function getModel(): string
    {
        return Nationality::class;
    }
}
