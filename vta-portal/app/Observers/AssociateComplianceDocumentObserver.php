<?php

namespace App\Observers;

use App\Models\AssociateComplianceDocument;
use App\Services\ActivityLogger;

class AssociateComplianceDocumentObserver
{
    public function created(AssociateComplianceDocument $doc): void
    {
        ActivityLogger::log(
            'compliance_document_uploaded',
            "Compliance document uploaded: {$doc->document_type}",
            $doc,
            associateId: $doc->associate_id,
            metadata: ['type' => $doc->document_type, 'expiry' => $doc->expiry_date?->toDateString()]
        );
    }
}
