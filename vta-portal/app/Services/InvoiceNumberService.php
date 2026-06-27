<?php

namespace App\Services;

use App\Models\VtaInvoice;

class InvoiceNumberService
{
    public function generate(): string
    {
        $year = now()->format('Y');

        $lastInvoice = VtaInvoice::where('invoice_number', 'like', "VTA-{$year}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice->invoice_number);
            $number = (int) end($parts) + 1;
        } else {
            $number = 1;
        }

        return sprintf('VTA-%s-%04d', $year, $number);
    }
}
