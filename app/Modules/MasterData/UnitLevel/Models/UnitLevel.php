<?php

namespace App\Modules\MasterData\UnitLevel\Models;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitLevel extends BaseModel
{
    use HasFactory;
    const COMPANY_TYPE = 'company';

    public $table = 'unit_levels';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        "name",
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
}
