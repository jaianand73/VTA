<?php

namespace App\Observers;

use App\Models\CaseNote;
use App\Services\ActivityLogger;

class CaseNoteObserver
{
    public function created(CaseNote $note): void
    {
        $patient = $note->patient;
        ActivityLogger::log(
            'case_note_uploaded',
            "Case note uploaded" . ($patient ? " for {$patient->first_name} {$patient->last_name}" : ''),
            $note,
            patientId: $note->patient_id,
            associateId: $note->associate_id,
            metadata: ['note_type' => $note->note_type]
        );
    }

    public function updated(CaseNote $note): void
    {
        if ($note->wasChanged('is_signed_off') && $note->is_signed_off) {
            $patient = $note->patient;
            ActivityLogger::log(
                'case_note_signed_off',
                "Case note signed off" . ($patient ? " for {$patient->first_name} {$patient->last_name}" : ''),
                $note,
                patientId: $note->patient_id,
                associateId: $note->associate_id
            );
            return;
        }

        if ($note->wasChanged('review_feedback')) {
            ActivityLogger::log(
                'case_note_feedback',
                "Revision feedback sent on case note",
                $note,
                patientId: $note->patient_id,
                associateId: $note->associate_id
            );
        }
    }
}
