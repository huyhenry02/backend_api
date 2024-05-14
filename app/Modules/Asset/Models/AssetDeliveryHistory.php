<?php

namespace App\Modules\Asset\Models;

use App\Models\BaseModel;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;


class AssetDeliveryHistory extends BaseModel implements Searchable
{
    public $table = 'asset_delivery_histories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        "asset_id",
        "created_date",
        "receiver",
        "deliver",
        "reason",
        "place_of_use",
        "attachments",
        "code",
        "status",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function asset(): HasOne
    {
        return $this->hasOne(Asset::class);
    }

    public function deliver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deliver');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver');
    }
    public function getSearchResult(): SearchResult
    {
        $type = 'AssetDelivery';
        return new \Spatie\Searchable\SearchResult(
            $this,
            $type,
        );
    }
}
