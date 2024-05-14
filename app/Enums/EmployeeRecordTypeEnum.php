<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum EmployeeRecordTypeEnum:string
{
    use EnumTrait;
    case CV = 'curriculum_vitae';
    case CONTRACT = 'contract';
    case HEALTH = 'health';
}
