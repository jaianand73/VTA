<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Associate;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    // ── Date Audit ────────────────────────────────────────────────────────────
    public function dateAudit(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(6)->toDateString());
        $dateTo   = $request->input('date_to',   today()->toDateString());

        $logs = ActivityLog::with('user', 'patient', 'associate')
            ->whereDate('occurred_at', '>=', $dateFrom)
            ->whereDate('occurred_at', '<=', $dateTo)
            ->orderBy('occurred_at', 'asc')
            ->get();

        $summary = $this->buildDateSummary($logs, $dateFrom, $dateTo);

        return view('audit.date', compact('dateFrom', 'dateTo', 'logs', 'summary'));
    }

    private function buildDateSummary($logs, string $dateFrom, string $dateTo): array
    {
        $days = \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1;

        $patientIds   = $logs->whereNotNull('patient_id')->pluck('patient_id')->unique();
        $associateIds = $logs->whereNotNull('associate_id')->pluck('associate_id')->unique();

        $newPatients   = $logs->where('subject_type', 'Patient')->where('action', 'patient_created')->count();
        $newEnquiries  = $logs->where('subject_type', 'Enquiry')->where('action', 'enquiry_created')->count();
        $sessions      = $logs->where('subject_type', 'Appointment')->where('action', 'appointment_status_changed')
                              ->filter(fn($l) => isset($l->metadata['to']) && $l->metadata['to'] === 'Completed')
                              ->count();
        $caseNotes     = $logs->where('subject_type', 'CaseNote')->where('action', 'case_note_uploaded')->count();
        $signedOff     = $logs->where('subject_type', 'CaseNote')->where('action', 'case_note_signed_off')->count();
        $invoices      = $logs->whereIn('subject_type', ['AssociateInvoice','VtaInvoice'])
                              ->whereIn('action', ['associate_invoice_submitted','vta_invoice_created'])->count();
        $fundingEvents = $logs->where('subject_type', 'FundingCycle')->where('action', 'funding_cycle_created')->count();
        $statusChanges = $logs->whereIn('action', ['patient_status_changed','enquiry_status_changed','appointment_status_changed'])->count();

        // Busiest day
        $byDay = $logs->groupBy(fn($l) => $l->occurred_at->format('Y-m-d'));
        $busiestDay = $byDay->sortByDesc(fn($g) => $g->count())->keys()->first();
        $busiestCount = $busiestDay ? $byDay[$busiestDay]->count() : 0;

        return compact(
            'days', 'newPatients', 'newEnquiries', 'sessions', 'caseNotes',
            'signedOff', 'invoices', 'fundingEvents', 'statusChanges',
            'patientIds', 'associateIds', 'busiestDay', 'busiestCount'
        );
    }

    // ── Patient Audit ─────────────────────────────────────────────────────────
    public function patientAudit(Request $request)
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'status', 'enquiry_id']);

        $patient    = null;
        $logs       = collect();
        $rangeLogs  = collect();
        $upcoming   = collect();
        $summary    = [];
        $allTime    = $request->boolean('all_time', true); // default all time for patient
        $dateFrom   = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo     = $request->input('date_to', today()->toDateString());

        if ($request->filled('patient_id')) {
            $patient = Patient::with(['caseManager', 'fundingCycles', 'patientAssociates.associate', 'assessment'])
                ->findOrFail($request->patient_id);

            // Enquiry-phase log IDs
            $enquiryLogIds = collect();
            if ($patient->enquiry_id) {
                $enquiryLogIds = ActivityLog::where('subject_type', 'Enquiry')
                    ->where('subject_id', $patient->enquiry_id)
                    ->pluck('id');
            }

            // All-time logs (always loaded for full journey)
            $logs = ActivityLog::with('user', 'associate')
                ->where(function ($q) use ($patient, $enquiryLogIds) {
                    $q->where('patient_id', $patient->id)
                      ->orWhereIn('id', $enquiryLogIds);
                })
                ->orderBy('occurred_at', 'asc')
                ->get();

            // Range logs for summary
            if (!$allTime) {
                $rangeLogs = $logs->filter(function ($l) use ($dateFrom, $dateTo) {
                    $d = $l->occurred_at->toDateString();
                    return $d >= $dateFrom && $d <= $dateTo;
                })->values();
            } else {
                $rangeLogs = $logs;
            }

            $summary = $this->buildPatientSummary($patient, $rangeLogs, $allTime);

            // Upcoming appointments
            $upcoming = DB::table('appointments')
                ->join('associates', 'appointments.associate_id', '=', 'associates.id')
                ->where('appointments.patient_id', $patient->id)
                ->where('appointments.scheduled_at', '>', now())
                ->where('appointments.status', 'Scheduled')
                ->orderBy('appointments.scheduled_at')
                ->select('appointments.*', 'associates.name as associate_name')
                ->get();
        }

        return view('audit.patient', compact(
            'patients', 'patient', 'logs', 'rangeLogs',
            'upcoming', 'summary', 'allTime', 'dateFrom', 'dateTo'
        ));
    }

    private function buildPatientSummary($patient, $logs, bool $allTime): array
    {
        $sessions   = $logs->where('subject_type', 'CaseNote')->where('action', 'case_note_uploaded')->count();
        $signedOff  = $logs->where('subject_type', 'CaseNote')->where('action', 'case_note_signed_off')->count();
        $statusChanges = $logs->where('action', 'patient_status_changed')->count();
        $comms      = $logs->where('subject_type', 'Communication')->count();
        $invoiced   = $logs->where('subject_type', 'VtaInvoice')->where('action', 'vta_invoice_created')
                           ->sum(fn($l) => $l->metadata['amount'] ?? 0);
        $fundingApproved = $logs->where('subject_type', 'FundingCycle')->where('action', 'funding_cycle_created')
                                ->sum(fn($l) => $l->metadata['amount'] ?? 0);

        // Stage transitions (from status_changed logs)
        $transitions = $logs->where('action', 'status_changed')
            ->whereNotNull('metadata')
            ->filter(fn($l) => isset($l->metadata['from']) && isset($l->metadata['to']))
            ->map(fn($l) => $l->metadata['from'] . ' → ' . $l->metadata['to'])
            ->values()->toArray();

        // Days in system — from patient creation to today, minimum 1
        $spanDays = (int) $patient->created_at->startOfDay()->diffInDays(now()->startOfDay()) + 1;

        return compact('sessions','signedOff','statusChanges','comms','invoiced','fundingApproved','transitions','spanDays');
    }

    // ── Associate Audit ───────────────────────────────────────────────────────
    public function associateAudit(Request $request)
    {
        $associates = Associate::orderBy('name')->get(['id', 'name', 'region', 'is_active']);

        $associate  = null;
        $logs       = collect();
        $rangeLogs  = collect();
        $upcoming   = collect();
        $stats      = [];
        $summary    = [];
        $allTime    = $request->boolean('all_time');
        $dateFrom   = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo     = $request->input('date_to', today()->toDateString());

        if ($request->filled('associate_id')) {
            $associate = Associate::with('complianceDocuments')->findOrFail($request->associate_id);

            $logsQuery = ActivityLog::with('user', 'patient')
                ->where('associate_id', $associate->id);

            if (!$allTime) {
                $logsQuery->whereDate('occurred_at', '>=', $dateFrom)
                          ->whereDate('occurred_at', '<=', $dateTo);
            }

            $logs = $logsQuery->orderBy('occurred_at', 'asc')->get();

            // For summary in ranged mode, use filtered logs; all_time uses all logs
            $rangeLogs = $logs;

            // Stats (always all-time totals)
            $activePatients = DB::table('patient_associates')
                ->where('associate_id', $associate->id)->whereNull('end_date')->count();
            $completedPatients = DB::table('patient_associates')
                ->where('associate_id', $associate->id)->whereNotNull('end_date')->count();
            $sessionsThisMonth = DB::table('case_notes')
                ->where('associate_id', $associate->id)
                ->whereYear('session_date', now()->year)
                ->whereMonth('session_date', now()->month)->count();
            $pendingInvoices = DB::table('associate_invoices')
                ->where('associate_id', $associate->id)
                ->whereIn('status', ['Received', 'Verified'])->count();
            $totalEarned = DB::table('associate_invoices')
                ->where('associate_id', $associate->id)
                ->where('status', 'Paid')->sum('total_amount');

            $stats = compact('activePatients','completedPatients','sessionsThisMonth','pendingInvoices','totalEarned');

            // Summary for the selected range
            $summary = $this->buildAssociateSummary($associate, $logs, $allTime, $dateFrom, $dateTo);

            // Upcoming appointments (next 14 days)
            $upcoming = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->where('appointments.associate_id', $associate->id)
                ->where('appointments.scheduled_at', '>', now())
                ->where('appointments.status', 'Scheduled')
                ->orderBy('appointments.scheduled_at')
                ->select('appointments.*', 'patients.first_name', 'patients.last_name')
                ->get();
        }

        return view('audit.associate', compact(
            'associates', 'associate', 'logs', 'rangeLogs',
            'upcoming', 'stats', 'summary', 'allTime', 'dateFrom', 'dateTo'
        ));
    }

    private function buildAssociateSummary($associate, $logs, bool $allTime, string $dateFrom, string $dateTo): array
    {
        $sessions      = $logs->where('subject_type', 'CaseNote')->where('action', 'case_note_uploaded')->count();
        $signedOff     = $logs->where('subject_type', 'CaseNote')->where('action', 'case_note_signed_off')->count();
        $patientsWorked = $logs->whereNotNull('patient_id')->pluck('patient_id')->unique()->count();
        $invoiceTotal  = $logs->where('subject_type', 'AssociateInvoice')->where('action', 'associate_invoice_submitted')
                              ->sum(fn($l) => $l->metadata['amount'] ?? 0);
        $invoicePaid   = $logs->where('subject_type', 'AssociateInvoice')->where('action', 'associate_invoice_paid')
                              ->sum(fn($l) => $l->metadata['amount'] ?? 0);
        $complianceDocs = $logs->where('subject_type', 'AssociateComplianceDocument')->count();
        $appointments  = $logs->where('subject_type', 'Appointment')->count();

        // Most active patient
        $topPatientId = $logs->whereNotNull('patient_id')
            ->groupBy('patient_id')
            ->sortByDesc(fn($g) => $g->count())
            ->keys()->first();
        $topPatient = $topPatientId
            ? DB::table('patients')->where('id', $topPatientId)->first(['first_name','last_name'])
            : null;

        $firstLog = $logs->sortBy('occurred_at')->first();
        $lastLog  = $logs->sortByDesc('occurred_at')->first();

        return compact('sessions','signedOff','patientsWorked','invoiceTotal','invoicePaid',
                       'complianceDocs','appointments','topPatient','firstLog','lastLog','allTime','dateFrom','dateTo');
    }
}
