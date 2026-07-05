<?php

namespace App\Observers;

use App\Models\Patient;
use App\Services\ActivityLogger;

class PatientObserver
{
    public function created(Patient $patient): void
    {
        ActivityLogger::log(
            'patient_created',
            "Patient record created: {$patient->first_name} {$patient->last_name}",
            $patient,
            patientId: $patient->id
        );
    }

    public function updated(Patient $patient): void
    {
        if ($patient->wasChanged('status')) {
            ActivityLogger::log(
                'patient_status_changed',
                "Patient status changed to {$patient->status}",
                $patient,
                patientId: $patient->id,
                metadata: ['from' => $patient->getOriginal('status'), 'to' => $patient->status]
            );
            return;
        }

        if ($patient->wasChanged('case_manager_id')) {
            ActivityLogger::log(
                'patient_updated',
                "Patient case manager reassigned",
                $patient,
                patientId: $patient->id
            );
            return;
        }

        ActivityLogger::log(
            'patient_updated',
            "Patient record updated: {$patient->first_name} {$patient->last_name}",
            $patient,
            patientId: $patient->id
        );
    }
}
