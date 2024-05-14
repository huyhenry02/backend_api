<?php

namespace App\Modules\Appointment\Transformers;

use App\Modules\Appointment\Models\Appointment;
use League\Fractal\TransformerAbstract;

class DetailAppointmentTransformer extends TransformerAbstract
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
            'name' => $appointment['name'],
            'email' => $appointment['email'],
            'reason' => $appointment['reason'],
            'reject_reason' => $appointment['reject_reason'],
            'status' => $appointment['status'],
            'employee' => $appointment->employee->curriculumVitae->name ?? '',
            'registerer' => $appointment->register->curriculumVitae->name ?? '',
            'start_time' => date('H:i d-m-Y', strtotime($appointment['start_time'])),
            'end_time' => date('H:i d-m-Y', strtotime($appointment['end_time'])),
            'phone' => $appointment['phone'],
            'identification' => $appointment['identification'],
        ];
    }
}
