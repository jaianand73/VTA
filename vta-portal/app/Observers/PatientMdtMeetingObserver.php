<?php

namespace App\Observers;

use App\Models\PatientMdtMeeting;
use App\Services\ActivityLogger;

class PatientMdtMeetingObserver
{
    public function created(PatientMdtMeeting $meeting): void
    {
        ActivityLogger::log(
            'mdt_meeting_recorded',
            "MDT meeting recorded" . ($meeting->meeting_date ? " on " . $meeting->meeting_date : ''),
            $meeting,
            patientId: $meeting->patient_id,
            metadata: ['meeting_date' => $meeting->meeting_date, 'attendees' => $meeting->attendees]
        );
    }

    public function deleted(PatientMdtMeeting $meeting): void
    {
        ActivityLogger::log(
            'mdt_meeting_deleted',
            "MDT meeting deleted" . ($meeting->meeting_date ? " (was on " . $meeting->meeting_date . ")" : ''),
            $meeting,
            patientId: $meeting->patient_id
        );
    }
}
