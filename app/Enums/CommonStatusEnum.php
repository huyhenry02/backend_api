<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum CommonStatusEnum:string
{
    use EnumTrait;
    case INACTIVE = "inactive";
    case ACTIVE = "active";
}
