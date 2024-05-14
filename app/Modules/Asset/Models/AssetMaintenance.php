<?php

namespace App\Modules\Asset\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;


class AssetMaintenance extends BaseModel implements Searchable
{
    public $table = 'asset_maintenances';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        "asset_id",
        "created_date",
        "created_by",
        "reason",
        "description",
        "proposal",
        "status",
        "code",
        "causal",
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

    public function getSearchResult(): SearchResult
    {
        $type = 'AssetMaintenance';
        return new \Spatie\Searchable\SearchResult(
            $this,
            $type,
        );
    }
}
