<?php

namespace App\Observers;

use App\Models\Enquiry;
use App\Services\ActivityLogger;

class EnquiryObserver
{
    public function created(Enquiry $enquiry): void
    {
        ActivityLogger::log(
            'enquiry_created',
            "Enquiry created from {$enquiry->enquirer_name}" . ($enquiry->company_name ? " ({$enquiry->company_name})" : ''),
            $enquiry
        );
    }

    public function updated(Enquiry $enquiry): void
    {
        if ($enquiry->wasChanged('status')) {
            ActivityLogger::log(
                'enquiry_status_changed',
                "Enquiry status changed to {$enquiry->status}",
                $enquiry,
                metadata: ['from' => $enquiry->getOriginal('status'), 'to' => $enquiry->status]
            );
        }

        if ($enquiry->wasChanged('qualified_as_referral') && $enquiry->qualified_as_referral) {
            ActivityLogger::log(
                'enquiry_qualified',
                "Enquiry marked as Qualified Referral",
                $enquiry
            );
        }

        if (!$enquiry->wasChanged('status') && !$enquiry->wasChanged('qualified_as_referral')) {
            ActivityLogger::log('enquiry_updated', "Enquiry details updated", $enquiry);
        }
    }
}
