<?php

namespace App\Modules\Employee\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface CurriculumVitaeInterface extends RepositoryInterface
{

    public function getChanges(string $id, array $logData);
}
