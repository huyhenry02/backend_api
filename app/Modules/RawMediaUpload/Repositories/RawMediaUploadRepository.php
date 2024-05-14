<?php

namespace App\Modules\RawMediaUpload\Repositories;

use App\Modules\RawMediaUpload\Models\RawMedia;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Repositories\BaseRepository;

class RawMediaUploadRepository extends BaseRepository implements RawMediaUploadInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return RawMedia::class;
    }

}
