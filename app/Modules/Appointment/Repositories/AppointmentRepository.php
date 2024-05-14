<?php

namespace App\Modules\Appointment\Repositories;

use App\Enums\AppointmentStatusEnum;
use App\Modules\Appointment\Models\Appointment;
use App\Modules\Appointment\Repositories\Interfaces\AppointmentInterface;
use App\Repositories\BaseRepository;

class AppointmentRepository extends BaseRepository implements AppointmentInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Appointment::class;
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
}
