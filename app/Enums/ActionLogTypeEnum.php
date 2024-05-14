<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum ActionLogTypeEnum:string
{
    use EnumTrait;
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
