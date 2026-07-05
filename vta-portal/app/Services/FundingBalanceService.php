<?php

namespace App\Services;

use App\Models\FundingCycle;
use App\Models\VtaInvoice;

class FundingBalanceService
{
    public function remainingBalance(FundingCycle $cycle): float
    {
        $paid = VtaInvoice::where('funding_cycle_id', $cycle->id)
            ->where('status', 'Paid')
            ->sum('total_amount');

        return max(0, $cycle->approved_amount - $paid);
    }

    public function invoicedAmount(FundingCycle $cycle): float
    {
        return VtaInvoice::where('funding_cycle_id', $cycle->id)
            ->where('status', 'Paid')
            ->sum('total_amount');
    }

    public function usagePercentage(FundingCycle $cycle): float
    {
        if ($cycle->approved_amount <= 0) {
            return 0;
        }

        $invoiced = $this->invoicedAmount($cycle);

        return min(100, round(($invoiced / $cycle->approved_amount) * 100, 1));
    }

    public function willExceedBalance(FundingCycle $cycle, float $amount): bool
    {
        return $amount > $this->remainingBalance($cycle);
    }

    public function isLowBalance(FundingCycle $cycle, float $threshold = 20.0): bool
    {
        return $cycle->approved_amount > 0
            && $this->usagePercentage($cycle) >= (100 - $threshold);
    }
}
