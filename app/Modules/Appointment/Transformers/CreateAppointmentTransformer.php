<?php

namespace App\Modules\Appointment\Transformers;

use App\Modules\Appointment\Models\Appointment;
use League\Fractal\TransformerAbstract;

class CreateAppointmentTransformer extends TransformerAbstract
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
            'employee_id' => $appointment['employee_id'],
            'registerer_id' => $appointment['registerer_id'],
            'name' => $appointment['name'],
            'email' => $appointment['email'],
            'registerer' => $appointment->register->curriculumVitae->name ?? '',
            'start_time' => date('H:i d-m-Y', strtotime($appointment['start_time'])),
            'end_time' => date('H:i d-m-Y', strtotime($appointment['end_time'])),
            'phone' => $appointment['phone'],
            'identification' => $appointment['identification'],
            'reason' => $appointment['reason'],
            'status' => $appointment['status'],
        ];
    }
}
