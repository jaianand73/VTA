<?php

namespace App\Observers;

use App\Models\FundingCycle;
use App\Services\ActivityLogger;

class FundingCycleObserver
{
    public function created(FundingCycle $cycle): void
    {
        ActivityLogger::log(
            'funding_cycle_created',
            "Funding cycle created" . ($cycle->approved_amount ? " — £" . number_format($cycle->approved_amount, 2) . " approved" : ''),
            $cycle,
            patientId: $cycle->patient_id ?? null,
            metadata: ['amount' => $cycle->approved_amount ?? null, 'funder' => $cycle->funder ?? null]
        );
    }

    public function updated(FundingCycle $cycle): void
    {
        ActivityLogger::log(
            'funding_cycle_updated',
            "Funding cycle updated",
            $cycle,
            patientId: $cycle->patient_id ?? null
        );
    }
}
