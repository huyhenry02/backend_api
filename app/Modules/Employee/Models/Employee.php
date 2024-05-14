<?php

namespace App\Modules\Employee\Models;

use App\Models\BaseModel;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Employee extends BaseModel implements Searchable
{

    use SoftDeletes;
    public $table = 'employees';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'code',
        'status',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * @return HasOne
     */
    public function curriculumVitae(): HasOne
    {
        return $this->hasOne(CurriculumVitae::class);
    }

    /**
     * @return HasMany
     */
    public function employeeLogs(): HasMany
    {
        return $this->hasMany(EmployeeLog::class);
    }

    public function health(): HasOne
    {
        return $this->hasOne(Health::class);
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function getSearchResult(): SearchResult
    {
        return new \Spatie\Searchable\SearchResult(
            $this,
            'employee',
        );
    }
}
