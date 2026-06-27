<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\AssociateInvoice;
use App\Models\EmailIntakeLog;
use App\Models\Appointment;
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

        $totalActiveCases = Patient::whereNotIn('status', ['Discharged', 'Case Closed'])->count();
        $needsReviewCount = Patient::where('needs_review', true)->count();
        $awaitingFundingCount = Patient::where('status', 'Awaiting Funding Approval')->count();

        $overdueInvoices = collect();
        $overdueAlerts = collect();
        if (Auth::user()->role === 'admin') {
            $overdueInvoices = AssociateInvoice::where('due_date', '<', now())
                ->whereNotIn('status', ['Paid', 'Cancelled'])
                ->get();
            $overdueAlerts = AssociateInvoice::whereBetween('due_date', [now(), now()->addDays(7)])
                ->whereIn('status', ['Received', 'Verified'])
                ->get();
        }

        $unprocessedEmails = EmailIntakeLog::where('processed', false)
            ->latest()
            ->take(5)
            ->get();

        $upcomingAppointments = Appointment::whereBetween('scheduled_at', [now(), now()->addDays(7)])
            ->get();

        $dailyActions = Patient::where('needs_review', true)
            ->orderBy('referral_date', 'asc')
            ->get();

        return view('dashboard.index', compact(
            'totalActiveCases', 'needsReviewCount', 'awaitingFundingCount',
            'overdueInvoices', 'unprocessedEmails', 'upcomingAppointments',
            'dailyActions', 'overdueAlerts'
        ));
    }
}
