<?php

namespace App\Http\Controllers;

use App\Models\VtaInvoice;
use App\Models\AssociateInvoice;
use App\Models\Company;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index()
    {
        return view('finance.index');
    }

    public function reports()
    {
        $revenueSummary = [
            'total_invoiced_month' => VtaInvoice::whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)->sum('total_amount'),
            'total_invoiced_year'  => VtaInvoice::whereYear('invoice_date', now()->year)->sum('total_amount'),
            'total_paid_month'     => VtaInvoice::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)->where('status', 'Paid')->sum('total_amount'),
            'total_paid_year'      => VtaInvoice::whereYear('payment_date', now()->year)
                ->where('status', 'Paid')->sum('total_amount'),
            'outstanding_0_30'     => VtaInvoice::whereIn('status', ['Sent', 'Overdue'])
                ->where('due_date', '>=', now()->subDays(30))->sum('total_amount'),
            'outstanding_31_60'    => VtaInvoice::whereIn('status', ['Sent', 'Overdue'])
                ->where('due_date', '<', now()->subDays(30))
                ->where('due_date', '>=', now()->subDays(60))->sum('total_amount'),
            'outstanding_61_plus'  => VtaInvoice::whereIn('status', ['Sent', 'Overdue'])
                ->where('due_date', '<', now()->subDays(60))->sum('total_amount'),
        ];

        $revenueByCompany = Company::with(['caseManagers.patients.vtaInvoices'])
            ->get()
            ->map(function ($company) {
                $totalInvoiced = 0;
                $totalPaid = 0;

                foreach ($company->caseManagers as $cm) {
                    foreach ($cm->patients as $patient) {
                        foreach ($patient->vtaInvoices as $inv) {
                            $totalInvoiced += $inv->total_amount;
                            if ($inv->status === 'Paid') {
                                $totalPaid += $inv->total_amount;
                            }
                        }
                    }
                }

                return [
                    'name'           => $company->name,
                    'total_invoiced' => $totalInvoiced,
                    'total_paid'     => $totalPaid,
                    'outstanding'    => $totalInvoiced - $totalPaid,
                ];
            });

        $associatePayments = AssociateInvoice::with('associate')
            ->selectRaw('associate_id, SUM(total_amount) as total_invoiced')
            ->selectRaw('SUM(CASE WHEN status = "Paid" THEN total_amount ELSE 0 END) as total_paid')
            ->selectRaw('SUM(CASE WHEN status IN ("Received","Verified") AND due_date < NOW() THEN total_amount ELSE 0 END) as overdue')
            ->groupBy('associate_id')
            ->get();

        return view('finance.reports', compact('revenueSummary', 'revenueByCompany', 'associatePayments'));
    }
}
