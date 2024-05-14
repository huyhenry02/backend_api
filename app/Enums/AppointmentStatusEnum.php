<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum AppointmentStatusEnum:string
{
    use EnumTrait;
    case PENDING = "pending";
    case APPROVED = "approved";
    case PROCESSING = "processing";
    case COMPLETED = "completed";
    case REJECTED = "rejected";
}
