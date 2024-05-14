<?php

namespace App\Modules\Asset\Models;

use App\Models\BaseModel;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Asset extends BaseModel implements Searchable
{
    public $table = 'assets';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        "name",
        "code",
        "management_code",
        "management_unit",
        "original_price",
        "residual_price",
        "insurance_contract",
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
    public function getSearchResult(): SearchResult
    {
        $type = 'Asset';
        return new \Spatie\Searchable\SearchResult(
            $this,
            $type,
        );
    }
}
