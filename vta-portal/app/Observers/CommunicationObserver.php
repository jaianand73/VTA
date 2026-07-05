<?php

namespace App\Observers;

use App\Models\Communication;
use App\Services\ActivityLogger;

class CommunicationObserver
{
    public function created(Communication $comm): void
    {
        ActivityLogger::log(
            'communication_logged',
            "Communication logged: {$comm->type}" . ($comm->subject ? " — {$comm->subject}" : ''),
            $comm,
            patientId: $comm->patient_id ?? null,
            associateId: $comm->patient_associate_id ?? null,
            metadata: ['type' => $comm->type, 'direction' => $comm->direction ?? null]
        );
    }
}
