<?php

namespace App\Modules\MasterData\Title\Repositories;

use App\Modules\MasterData\Title\Models\Title;
use App\Modules\MasterData\Title\Repositories\Interfaces\TitleInterface;
use App\Repositories\BaseRepository;

class TitleRepository extends BaseRepository implements TitleInterface
{
    public function getModel(): string
    {
        return Title::class;
    }
}
