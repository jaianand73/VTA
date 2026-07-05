<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\ActivityLogger;

class AppointmentObserver
{
    public function created(Appointment $appt): void
    {
        ActivityLogger::log(
            'appointment_created',
            "Appointment booked" . ($appt->patient_id ? " for patient #{$appt->patient_id}" : ''),
            $appt,
            patientId: $appt->patient_id ?? null,
            associateId: $appt->associate_id ?? null,
            metadata: ['date' => $appt->appointment_date ?? null, 'type' => $appt->appointment_type ?? null]
        );
    }

    public function updated(Appointment $appt): void
    {
        if ($appt->wasChanged('status')) {
            ActivityLogger::log(
                'appointment_status_changed',
                "Appointment status changed to {$appt->status}",
                $appt,
                patientId: $appt->patient_id ?? null,
                associateId: $appt->associate_id ?? null,
                metadata: ['from' => $appt->getOriginal('status'), 'to' => $appt->status]
            );
        }
    }
}
