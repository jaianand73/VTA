<?php

namespace App\Http\Controllers;

use App\Models\AssociateInvoice;
use App\Models\CaseNote;
use App\Models\EmailIntakeLog;
use App\Models\Enquiry;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'associate') {
            return redirect()->route('associate-portal.dashboard');
        }
        if ($user->role === 'case_manager') {
            return redirect()->route('case-manager-portal.dashboard');
        }

        $overdueAlerts = collect();
        if (Auth::user()->role === 'admin') {
            $overdueAlerts = AssociateInvoice::whereBetween('due_date', [now(), now()->addDays(7)])
                ->whereIn('status', ['Received', 'Verified'])
                ->get();
        }

        $unprocessedEmails = EmailIntakeLog::where('processed', false)
            ->latest()
            ->take(5)
            ->get();

        // F1 — Dashboard widgets counts
        $unprocessedEmailsCount = EmailIntakeLog::where('processed', false)->count();
        $clinicalHeadReview = CaseNote::where('needs_review', true)->where('is_signed_off', false)->count();
        $pendingEnquiries = Enquiry::whereIn('status', ['New', 'In Progress', 'Qualified'])->count();
        $invoicesDue = AssociateInvoice::where('due_date', '<', now())->where('status', '!=', 'Paid')->count();

        // Q54 — associate compliance expiry alerts (expired or expiring within 90 days)
        $expiringCompliance = \App\Models\AssociateComplianceDocument::with('associate')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('expiry_date', '>=', now()->subDays(30))
            ->orderBy('expiry_date')
            ->get();

        // Q65 — associate invoices submitted via portal awaiting admin review
        $pendingAssocInvoices = \App\Models\AssociateInvoice::where('status', 'Submitted')
            ->with('associate')
            ->latest()
            ->get();

        // Q32 — case notes needing clinical head review (list for detail panel)
        $reviewNotes = CaseNote::where('needs_review', true)
            ->where('is_signed_off', false)
            ->with(['patient', 'associate'])
            ->latest('session_date')
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'overdueAlerts', 'unprocessedEmails',
            'unprocessedEmailsCount', 'clinicalHeadReview',
            'pendingEnquiries', 'invoicesDue',
            'expiringCompliance', 'pendingAssocInvoices', 'reviewNotes'
        ));
    }
}
