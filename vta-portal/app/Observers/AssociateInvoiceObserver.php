<?php

namespace App\Observers;

use App\Models\AssociateInvoice;
use App\Services\ActivityLogger;

class AssociateInvoiceObserver
{
    public function created(AssociateInvoice $invoice): void
    {
        ActivityLogger::log(
            'associate_invoice_submitted',
            "Associate invoice submitted" . ($invoice->total_amount ? " (£" . number_format($invoice->total_amount, 2) . ")" : ''),
            $invoice,
            patientId: $invoice->patient_id ?? null,
            associateId: $invoice->associate_id ?? null,
            metadata: ['status' => $invoice->status, 'amount' => $invoice->total_amount ?? null]
        );
    }

    public function updated(AssociateInvoice $invoice): void
    {
        if ($invoice->wasChanged('status')) {
            $action = $invoice->status === 'Paid' ? 'associate_invoice_paid' : 'associate_invoice_updated';
            ActivityLogger::log(
                $action,
                "Associate invoice status changed to {$invoice->status}",
                $invoice,
                patientId: $invoice->patient_id ?? null,
                associateId: $invoice->associate_id ?? null,
                metadata: ['from' => $invoice->getOriginal('status'), 'to' => $invoice->status]
            );
        }
    }
}
