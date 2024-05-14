<?php

namespace App\Modules\RawMediaUpload\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;

class RawMedia extends Model implements HasMedia
{
    use InteractsWithMedia;

    public $table = 'raw_media';

    public $incrementing = false;
    protected $keyType = 'uuid';

    /**
     * The primary key for the model.
     *
     * @var string
     */

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'updated_at',
        'created_at',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });
    }

    public function registerMediaCollections(): void
    {
        foreach (RawMediaUploadCollectionConstants::getAllValues() as $collection) {
            $this->addMediaCollection($collection);
        }
    }
}
