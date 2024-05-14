<?php

namespace App\Modules\Employee\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface EmployeeInterface extends RepositoryInterface
{
    public function create(array $attributes);
    public function update($id, array $attributes);

}
