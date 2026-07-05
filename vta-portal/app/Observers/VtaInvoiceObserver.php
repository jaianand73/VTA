<?php

namespace App\Observers;

use App\Models\VtaInvoice;
use App\Services\ActivityLogger;

class VtaInvoiceObserver
{
    public function created(VtaInvoice $invoice): void
    {
        ActivityLogger::log(
            'vta_invoice_created',
            "VTA invoice raised" . ($invoice->amount ? " (£" . number_format($invoice->amount, 2) . ")" : ''),
            $invoice,
            patientId: $invoice->patient_id ?? null,
            metadata: ['status' => $invoice->status, 'amount' => $invoice->amount ?? null]
        );
    }

    public function updated(VtaInvoice $invoice): void
    {
        if ($invoice->wasChanged('status')) {
            $action = $invoice->status === 'Paid' ? 'vta_invoice_paid' : 'vta_invoice_updated';
            ActivityLogger::log(
                $action,
                "VTA invoice status changed to {$invoice->status}",
                $invoice,
                patientId: $invoice->patient_id ?? null,
                metadata: ['from' => $invoice->getOriginal('status'), 'to' => $invoice->status]
            );
        }
    }
}
