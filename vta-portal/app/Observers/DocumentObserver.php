<?php

namespace App\Observers;

use App\Models\Document;
use App\Services\ActivityLogger;

class DocumentObserver
{
    public function created(Document $document): void
    {
        ActivityLogger::log(
            'document_uploaded',
            "Document uploaded: {$document->original_filename}",
            $document,
            patientId: $document->patient_id ?? null,
            metadata: ['type' => $document->document_type_id, 'filename' => $document->original_filename]
        );
    }

    public function deleted(Document $document): void
    {
        ActivityLogger::log(
            'document_deleted',
            "Document deleted: {$document->original_filename}",
            $document,
            patientId: $document->patient_id ?? null
        );
    }
}
