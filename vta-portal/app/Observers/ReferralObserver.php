<?php

namespace App\Observers;

use App\Models\Referral;
use App\Services\ActivityLogger;

class ReferralObserver
{
    public function created(Referral $referral): void
    {
        ActivityLogger::log(
            'referral_created',
            "Referral created: {$referral->patient_full_name}",
            $referral,
            metadata: ['ref' => $referral->referral_ref, 'status' => $referral->status]
        );
    }

    public function updated(Referral $referral): void
    {
        if ($referral->wasChanged('status')) {
            ActivityLogger::log(
                'referral_status_changed',
                "Referral status changed to {$referral->status}",
                $referral,
                metadata: ['from' => $referral->getOriginal('status'), 'to' => $referral->status]
            );
            return;
        }

        if ($referral->wasChanged('visit_approved_date')) {
            ActivityLogger::log(
                'referral_visit_approved',
                "Go-ahead to Visit recorded for {$referral->patient_full_name}",
                $referral,
                metadata: ['date' => $referral->visit_approved_date?->toDateString()]
            );
            return;
        }

        if ($referral->wasChanged('proposal_submitted_date')) {
            ActivityLogger::log(
                'referral_proposal_submitted',
                "Proposal submitted for {$referral->patient_full_name}",
                $referral,
                metadata: ['date' => $referral->proposal_submitted_date?->toDateString()]
            );
            return;
        }

        ActivityLogger::log(
            'referral_updated',
            "Referral updated: {$referral->patient_full_name}",
            $referral
        );
    }
}
