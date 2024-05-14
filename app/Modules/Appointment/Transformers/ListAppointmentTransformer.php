<?php

namespace App\Modules\Appointment\Transformers;

use App\Modules\Appointment\Models\Appointment;
use League\Fractal\TransformerAbstract;

class ListAppointmentTransformer extends TransformerAbstract
{
    /**
     *
     * @param Appointment $appointment
     *
     * @return array
     */
    public function transform(Appointment $appointment): array
    {
        return [
            'id' => $appointment['id'],
            'name' => $appointment['name'],
            'email' => $appointment['email'],
            'start_time' => date('H:i d-m-Y', strtotime($appointment['start_time'])),
            'end_time' => date('H:i d-m-Y', strtotime($appointment['end_time'])),
            'status' => $appointment['status'],
        ];
    }
}
