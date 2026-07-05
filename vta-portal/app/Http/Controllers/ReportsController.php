<?php
namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FundingCycle;
use App\Models\VtaInvoice;
use App\Models\AssociateInvoice;
use App\Models\ReferralBill;
use App\Models\Enquiry;
use App\Models\CaseNote;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function fundingBalanceSummary(): View
    {
        $activePatients = Patient::with(['fundingCycles', 'assessment'])->get();
        return view('reports.funding-balance', compact('activePatients'));
    }

    public function financialSummary(Request $request): View
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

        $vtaInvoiced = VtaInvoice::whereBetween('invoice_date', [$from, $to])->sum('total_amount');
        $vtaPaid = VtaInvoice::where('status', 'Paid')->whereBetween('invoice_date', [$from, $to])->sum('total_amount');
        $associateInvoiced = AssociateInvoice::whereBetween('invoice_date', [$from, $to])->sum('total_amount');
        $associatePaid = AssociateInvoice::where('status', 'Paid')->whereBetween('invoice_date', [$from, $to])->sum('total_amount');

        $referralBillsTotal = ReferralBill::whereBetween('bill_date', [$from, $to])->sum('amount');
        $referralBillsPaid  = ReferralBill::where('status', 'Paid')->whereBetween('bill_date', [$from, $to])->sum('amount');

        return view('reports.financial-summary', compact(
            'from', 'to',
            'vtaInvoiced', 'vtaPaid',
            'associateInvoiced', 'associatePaid',
            'referralBillsTotal', 'referralBillsPaid'
        ));
    }

    public function activePatientsByStatus(): View
    {
        $patients = Patient::selectRaw('status, count(*) as count')->groupBy('status')->orderBy('status')->get();
        return view('reports.patients-by-status', compact('patients'));
    }

    public function masterLog(): View
    {
        $rows = Patient::with([
            'caseManager.company',
            'fundingCycles',
            'vtaInvoices',
            'associateInvoices',
        ])
        ->whereNotIn('status', ['Not Proceeding', 'Case Closed'])
        ->orderBy('last_name')
        ->get()
        ->map(function ($p) {
            $activeCycle    = $p->fundingCycles->where('is_active', true)->first();
            $approved       = $activeCycle?->approved_amount ?? 0;
            $vtaPaid        = $p->vtaInvoices->where('status', 'Paid')->sum('total_amount');
            $assocPaid      = $p->associateInvoices->where('status', 'Paid')->sum('total_amount');
            $totalExpenses  = $vtaPaid + $assocPaid;
            $balance        = max(0, $approved - $totalExpenses);

            return [
                'patient'     => $p,
                'approved'    => $approved,
                'vta_paid'    => $vtaPaid,
                'assoc_paid'  => $assocPaid,
                'balance'     => $balance,
            ];
        });

        return view('reports.master-log', compact('rows'));
    }

    public function associateActivity(Request $request): View
    {
        $from = $request->get('from', now()->subMonths(6)->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $notes = CaseNote::with('associate')
            ->whereBetween('session_date', [$from, $to])
            ->selectRaw('associate_id, count(*) as total_notes, sum(case when is_signed_off then 1 else 0 end) as signed_off')
            ->groupBy('associate_id')
            ->get()
            ->keyBy('associate_id');

        // Referral sessions in period (by associate via referral)
        $refSessions = ReferralBill::selectRaw('referrals.associate_id, count(*) as total_sessions')
            ->join('referrals', 'referral_bills.referral_id', '=', 'referrals.id')
            ->whereBetween('referral_bills.bill_date', [$from, $to])
            ->whereNotNull('referrals.associate_id')
            ->groupBy('referrals.associate_id')
            ->pluck('total_sessions', 'referrals.associate_id');

        $refSessionCounts = \App\Models\ReferralSession::selectRaw('referrals.associate_id, count(*) as cnt')
            ->join('referrals', 'referral_sessions.referral_id', '=', 'referrals.id')
            ->whereBetween('referral_sessions.session_date', [$from, $to])
            ->whereNotNull('referrals.associate_id')
            ->groupBy('referrals.associate_id')
            ->pluck('cnt', 'referrals.associate_id');

        $refBillTotals = ReferralBill::selectRaw('referrals.associate_id, sum(amount) as total_billed')
            ->join('referrals', 'referral_bills.referral_id', '=', 'referrals.id')
            ->whereBetween('referral_bills.bill_date', [$from, $to])
            ->whereNotNull('referrals.associate_id')
            ->groupBy('referrals.associate_id')
            ->pluck('total_billed', 'referrals.associate_id');

        // Merge all associate IDs
        $allAssociateIds = $notes->keys()
            ->merge($refSessionCounts->keys())
            ->unique();

        $associates = \App\Models\Associate::whereIn('id', $allAssociateIds)->orderBy('name')->get()->keyBy('id');

        return view('reports.associate-activity', compact(
            'from', 'to', 'notes', 'refSessionCounts', 'refBillTotals', 'associates', 'allAssociateIds'
        ));
    }
}
