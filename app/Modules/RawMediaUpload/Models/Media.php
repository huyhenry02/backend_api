<?php

namespace App\Modules\RawMediaUpload\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;


class Media extends BaseMedia
{
    public $table = 'media';
}
