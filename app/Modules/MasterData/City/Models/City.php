<?php

namespace App\Modules\MasterData\City\Models;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends BaseModel
{
    use HasFactory;
    public $table = 'cities';
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