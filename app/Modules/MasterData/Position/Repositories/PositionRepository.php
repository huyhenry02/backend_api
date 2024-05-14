<?php

namespace App\Modules\MasterData\Position\Repositories;

use App\Modules\MasterData\Position\Models\Position;
use App\Modules\MasterData\Position\Repositories\Interfaces\PositionInterface;
use App\Repositories\BaseRepository;

class PositionRepository extends BaseRepository implements PositionInterface
{
    public function getModel(): string
    {
        return Position::class;
    }
}
