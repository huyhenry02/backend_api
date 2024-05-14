<?php

namespace App\Modules\Example\Repositories;

use App\Modules\Example\Models\Example;
use App\Modules\Example\Repositories\Interfaces\ExampleInterface;
use App\Repositories\BaseRepository;

class ExampleRepository extends BaseRepository implements ExampleInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Example::class;
    }

}
