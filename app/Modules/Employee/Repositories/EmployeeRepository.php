<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\BaseRepository;

class EmployeeRepository extends BaseRepository implements EmployeeInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Employee::class;
    }

    public function update($id, array $attributes): mixed
    {
        $response = $this->find($id);
        if ($response) {
            $response->update($attributes);
            return $response;
        }
        return false;
    }

    public function create(array $attributes): mixed
    {
        $type = $attributes['type'] ?? '';
        if ($type !== GUEST_TYPE) $type = EMPLOYEE_TYPE;

        return $this->_model->create([
            'code' => $attributes['code'],
            'type' => $type,
        ]);
    }
}
