<?php

namespace App\Modules\Sequence\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface SequenceInterface extends RepositoryInterface
{
    public function generateCode(string $type = EMPLOYEE_CODE, int $length = SEQUENCE_DEFAULT_LENGTH): string;
}
