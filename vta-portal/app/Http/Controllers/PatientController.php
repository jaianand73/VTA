<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientAssociate;
use App\Models\Associate;
use App\Models\Communication;
use App\Models\Document;
use App\Models\CostEstimation;
use App\Models\FundingCycle;
use App\Models\CaseManager;
use App\Models\Enquiry;
use App\Services\FundingBalanceService;
use App\Services\PatientTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

        $query = Patient::with(['caseManager.company', 'assignedStaff', 'patientAssociates.associate']);

        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        if ($request->filled('needs_review')) {
            $query->where('needs_review', filter_var($request->needs_review, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('assigned_staff')) {
            $query->where('assigned_staff_id', $request->assigned_staff);
        }

        if ($request->filled('associate')) {
            $query->whereHas('patientAssociates', function ($q) use ($request) {
                $q->where('associate_id', $request->associate);
            });
        }

        if ($request->filled('company')) {
            $query->whereHas('caseManager', function ($q) use ($request) {
                $q->where('company_id', $request->company);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('referral_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('referral_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('condition', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(20);
        $staffUsers = \App\Models\User::whereIn('role', ['admin', 'staff'])->get();
        $associates = \App\Models\Associate::all();
        $companies = \App\Models\Company::all();

        return view('patients.index', compact('patients', 'staffUsers', 'associates', 'companies'));
    }

    public function create(Request $request)
    {
        $enquiry = null;
        if ($request->has('enquiry_id')) {
            $enquiry = Enquiry::find($request->enquiry_id);
        }

        return view('patients.create', compact('enquiry'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);

        $data = $request->validate([
            'patient_ref' => 'nullable|string|max:50',
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'referral_date' => 'nullable|date',
            'first_contact_date' => 'nullable|date',
            'discharge_date' => 'nullable|date',
            'fee_agreed_amount' => 'nullable|numeric|min:0',
            'fee_agreed_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'assessment_report_sent' => 'nullable|boolean',
            'assessment_report_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'invoice_recipient_type' => 'nullable|in:Case Manager Company,Solicitor,Insurance Company,Other',
            'invoice_recipient_name' => 'nullable|string|max:255',
            'invoice_recipient_email' => 'nullable|email|max:255',
            'invoice_recipient_address' => 'nullable|string',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'folder_path' => 'nullable|string|max:255',
            'enquiry_id' => 'nullable|exists:enquiries,id',
            'reason_for_referral' => 'nullable|string|max:100',
            'referrers' => 'nullable|array',
            'referrers.*.name' => 'required_with:referrers.*.role|string|max:255',
            'referrers.*.role' => 'nullable|string|max:255',
            'referrers.*.company_name' => 'nullable|string|max:255',
            'referrers.*.address' => 'nullable|string',
            'referrers.*.email' => 'nullable|email|max:255',
            'referrers.*.phone' => 'nullable|string|max:50',
            'referrers.*.special_instructions' => 'nullable|string',
            'next_of_kin' => 'nullable|array',
            'next_of_kin.*.name' => 'nullable|string|max:255',
            'next_of_kin.*.relationship' => 'nullable|string|max:100',
            'next_of_kin.*.email' => 'nullable|email|max:255',
            'next_of_kin.*.phone' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('fee_agreed_document')) {
            $data['fee_agreed_document'] = $request->file('fee_agreed_document')->store('patient-documents', 'public');
        }
        if ($request->hasFile('assessment_report_document')) {
            $data['assessment_report_document'] = $request->file('assessment_report_document')->store('patient-documents', 'public');
        }
        $data['assessment_report_sent'] = $request->boolean('assessment_report_sent');
        $data['referral_date'] = $data['referral_date'] ?? now()->toDateString();
        $data['created_by'] = Auth::id();

        if ($request->filled('enquiry_id') && empty($data['patient_ref'])) {
            $sourceEnquiry = Enquiry::find($request->enquiry_id);
            if ($sourceEnquiry && $sourceEnquiry->enquiry_ref) {
                $data['patient_ref'] = $sourceEnquiry->enquiry_ref;
            }
        }

        $patient = Patient::create($data);

        if ($request->filled('enquiry_id')) {
            $patient->enquiry_id = $request->enquiry_id;
            $patient->save();

            Enquiry::find($request->enquiry_id)->update(['status' => 'Converted']);

            Document::where('enquiry_id', $request->enquiry_id)
                ->whereNull('patient_id')
                ->update(['patient_id' => $patient->id]);

            Communication::where('enquiry_id', $request->enquiry_id)
                ->whereNull('patient_id')
                ->update(['patient_id' => $patient->id]);
        }

        foreach ($request->referrers ?? [] as $referrer) {
            if (!empty($referrer['name'])) {
                $patient->referrers()->create($referrer);
            }
        }

        foreach ($request->next_of_kin ?? [] as $nok) {
            if (!empty($nok['name'])) {
                $patient->nextOfKin()->create($nok);
            }
        }

        return redirect()->route('patients.show', $patient);
    }

    public function show(Patient $patient, FundingBalanceService $fundingBalanceService)
    {
        $this->authorize('view', $patient);

        $patient->load([
            'caseManager.company',
            'assignedStaff',
            'createdBy',
            'patientAssociates.associate',
            'fundingCycles',
            'documents.documentType',
            'costEstimations',
            'referrers',
            'nextOfKin',
            'communications' => function ($q) {
                $q->latest();
            },
            'caseNotes' => function ($q) {
                $q->with(['associate', 'signedOffBy'])->latest('session_date');
            },
            'appointments' => function ($q) {
                $q->with('activityType')->latest('scheduled_at');
            },
        ]);

        $associates = \App\Models\Associate::where('is_active', true)->orderBy('name')->get();
        $documentTypes = \App\Models\DocumentType::where('is_active', true)->orderBy('sort_order')->get();

        $timeline = collect()
            ->merge($patient->communications->map(fn($c) => ['date' => $c->communication_date ?? $c->created_at, 'type' => 'Communication', 'icon' => 'fa-message', 'desc' => $c->subject, 'color' => 'bg-blue-100 text-blue-700']))
            ->merge($patient->documents->map(fn($d) => ['date' => $d->created_at, 'type' => 'Document', 'icon' => 'fa-file', 'desc' => $d->file_name, 'color' => 'bg-amber-100 text-amber-700']))
            ->merge($patient->caseNotes->map(fn($n) => ['date' => $n->session_date ?? $n->created_at, 'type' => 'Case Note', 'icon' => 'fa-note-sticky', 'desc' => $n->note_type, 'color' => 'bg-purple-100 text-purple-700']))
            ->merge($patient->appointments->map(fn($a) => ['date' => $a->scheduled_at, 'type' => 'Appointment', 'icon' => 'fa-calendar', 'desc' => $a->activityType?->name ?? 'Appointment', 'color' => 'bg-green-100 text-green-700']))
            ->merge($patient->vtaInvoices->map(fn($i) => ['date' => $i->invoice_date ?? $i->created_at, 'type' => 'Invoice', 'icon' => 'fa-file-invoice', 'desc' => 'VTA ' . ($i->invoice_number ?? '') . ' — £' . number_format($i->total_amount, 2), 'color' => 'bg-teal-100 text-teal-700']))
            ->sortByDesc('date')
            ->values();

        $allowedTransitions = $this->allowedTransitions()[$patient->status] ?? [];

        return view('patients.show', compact('patient', 'associates', 'documentTypes', 'fundingBalanceService', 'timeline', 'allowedTransitions'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'patient_ref' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'referral_date' => 'nullable|date',
            'first_contact_date' => 'nullable|date',
            'discharge_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'reason_for_referral' => 'nullable|string|max:100',
        ]);

        $data['referral_date'] = $data['referral_date'] ?? $patient->referral_date;

        $patient->update($data);

        return redirect()->route('patients.show', $patient);
    }

    public function updateNotes(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $patient->update($data);

        return redirect()->back()->with('success', 'Notes saved.');
    }

    public function updateStatus(Request $request, Patient $patient)
    {
        $this->authorize('changeStatus', $patient);

        $data = $request->validate([
            'status' => 'required|string|max:50',
            'discharge_date' => 'nullable|date',
        ]);

        $currentStatus = $patient->status;
        $newStatus = $data['status'];

        if ($currentStatus === $newStatus) {
            return redirect()->back();
        }

        $allowed = $this->allowedTransitions();
        $nextStatuses = $allowed[$currentStatus] ?? [];

        if (!in_array($newStatus, $nextStatuses)) {
            return back()->withErrors(['status' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'."]);
        }

        if ($newStatus === 'Funding Approved') {
            $hasCycle = FundingCycle::where('patient_id', $patient->id)
                ->whereNotNull('approval_document_path')
                ->exists();
            if (!$hasCycle) {
                return back()->withErrors(['status' => 'Cannot approve funding: no funding approval document uploaded.']);
            }
        }

        if ($newStatus === 'Treatment Active') {
            if ($patient->status !== 'Funding Approved') {
                return back()->withErrors(['status' => 'Treatment can only start from Funding Approved status.']);
            }
        }

        if ($newStatus === 'Case Closed') {
            $unpaidCount = $patient->vtaInvoices()->where('status', '!=', 'Paid')->count();
            if ($unpaidCount > 0) {
                $total = $patient->vtaInvoices()->where('status', '!=', 'Paid')->sum('total_amount');
                return back()->withErrors(['status' => "{$unpaidCount} unpaid invoice(s) totalling £" . number_format($total, 2) . " must be settled before closing the case."]);
            }
        }

        $patient->update($data);

        return redirect()->back();
    }

    private function allowedTransitions(): array
    {
        return [
            'Enquiry Logged'            => ['Response Sent', 'Not Proceeding'],
            'Response Sent'             => ['Awaiting LOI', 'Not Proceeding'],
            'Awaiting LOI'              => ['LOI Received', 'Not Proceeding'],
            'LOI Received'              => ['Assessment Scheduled'],
            'Assessment Scheduled'      => ['Assessment Completed'],
            'Assessment Completed'      => ['Report Drafted'],
            'Report Drafted'            => ['Report Sent'],
            'Report Sent'               => ['Cost Estimation Sent'],
            'Cost Estimation Sent'      => ['Awaiting Funding Approval'],
            'Awaiting Funding Approval' => ['Funding Approved'],
            'Funding Approved'          => ['Treatment Active', 'Under Treatment'],
            'Treatment Active'          => ['On Hold', 'Awaiting Further Funding', 'Discharged', 'Under Treatment'],
            'Under Treatment'           => ['On Hold', 'Awaiting Refunding', 'Discharged'],
            'On Hold'                   => ['Treatment Active', 'Under Treatment', 'Awaiting Further Funding'],
            'Awaiting Further Funding'  => ['Funding Approved'],
            'Discharged'                => ['Case Closed', 'Awaiting Refunding'],
            'Awaiting Refunding'        => ['Refunding Granted'],
            'Refunding Granted'         => ['Closed', 'Case Closed'],
            'Case Closed'               => [],
            'Closed'                    => [],
        ];
    }

    public function updateClinicalAlert(Request $request, Patient $patient)
    {
        $request->validate(['clinical_alert' => 'nullable|string|max:1000']);
        $patient->update(['clinical_alert' => $request->clinical_alert]);
        return redirect()->back()->with('success', 'Clinical alert updated.');
    }

    public function transfer(Request $request, Patient $patient, PatientTransferService $transferService)
    {
        $this->authorize('transfer', $patient);

        $data = $request->validate([
            'case_manager_id' => 'required|exists:case_managers,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $transferService->transfer($patient, $data['case_manager_id'], $data['reason'] ?? null);

        return redirect()->back()->with('success', 'Patient transferred successfully.');
    }

    public function assignStaff(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'assigned_staff_id' => 'nullable|exists:users,id',
        ]);

        $previousStaff = $patient->assignedStaff;
        $patient->update($data);
        $patient->refresh();
        $newStaff = $patient->assignedStaff;

        $from = $previousStaff?->name ?? 'Unassigned';
        $to   = $newStaff?->name ?? 'Unassigned';

        \App\Services\ActivityLogger::log(
            'staff_assigned',
            "Assigned staff changed from {$from} to {$to}",
            $patient,
            patientId: $patient->id,
            metadata: ['from' => $from, 'to' => $to]
        );

        return redirect()->back()->with('success', "Staff assignment updated: {$to} is now assigned to this patient.");
    }

    public function updateNok(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $request->validate([
            'next_of_kin' => 'nullable|array',
            'next_of_kin.*.name' => 'nullable|string|max:255',
            'next_of_kin.*.relationship' => 'nullable|string|max:100',
            'next_of_kin.*.email' => 'nullable|email|max:255',
            'next_of_kin.*.phone' => 'nullable|string|max:50',
        ]);

        $patient->nextOfKin()->delete();
        foreach ($request->next_of_kin ?? [] as $nok) {
            if (!empty($nok['name'])) {
                $patient->nextOfKin()->create($nok);
            }
        }

        return redirect()->back()->with('success', 'Next of Kin updated.');
    }

    public function updateReferrers(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $request->validate([
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'referrers' => 'nullable|array',
            'referrers.*.name' => 'nullable|string|max:255',
            'referrers.*.role' => 'nullable|string|max:255',
            'referrers.*.company_name' => 'nullable|string|max:255',
            'referrers.*.address' => 'nullable|string',
            'referrers.*.email' => 'nullable|email|max:255',
            'referrers.*.phone' => 'nullable|string|max:50',
            'referrers.*.special_instructions' => 'nullable|string',
        ]);

        $patient->update(['case_manager_id' => $request->case_manager_id]);

        $patient->referrers()->delete();
        foreach ($request->referrers ?? [] as $referrer) {
            if (!empty($referrer['name'])) {
                $patient->referrers()->create($referrer);
            }
        }

        return redirect()->back()->with('success', 'Referrers updated.');
    }

    public function updateAccounts(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'fee_agreed_amount' => 'nullable|numeric|min:0',
            'fee_agreed_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'assessment_report_sent' => 'nullable|boolean',
            'assessment_report_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'invoice_recipient_type' => 'nullable|in:Case Manager Company,Solicitor,Insurance Company,Other',
            'invoice_recipient_name' => 'nullable|string|max:255',
            'invoice_recipient_email' => 'nullable|email|max:255',
            'invoice_recipient_address' => 'nullable|string',
        ]);

        if ($request->hasFile('fee_agreed_document')) {
            $data['fee_agreed_document'] = $request->file('fee_agreed_document')->store('patient-documents', 'public');
        }
        if ($request->hasFile('assessment_report_document')) {
            $data['assessment_report_document'] = $request->file('assessment_report_document')->store('patient-documents', 'public');
        }
        $data['assessment_report_sent'] = $request->boolean('assessment_report_sent');

        $patient->update($data);

        return redirect()->back()->with('success', 'Accounts / Financial details updated.');
    }

    public function toggleNeedsReview(Patient $patient)
    {
        $patient->update(['needs_review' => !$patient->needs_review]);

        return redirect()->back();
    }

    public function addAssociate(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'associate_id' => 'required|exists:associates,id',
            'role' => 'required|string|in:Assessment,Treatment,Supervision,MDT',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $existing = PatientAssociate::where('patient_id', $patient->id)
            ->where('role', $data['role'])
            ->whereNull('end_date')
            ->exists();

        if ($existing) {
            return back()->withErrors(['role' => "Patient already has an active {$data['role']} associate."]);
        }

        $data['patient_id'] = $patient->id;
        $data['assigned_by'] = Auth::id();

        PatientAssociate::create($data);

        $associate = Associate::find($request->associate_id);

        Communication::create([
            'patient_id' => $patient->id,
            'type'       => 'Other',
            'direction'  => 'Outbound',
            'subject'    => 'Associate allocated',
            'summary'    => ($associate->name ?? 'Unknown') . ' allocated as ' . $request->role . ' on ' . now()->toDateString(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->back();
    }
}
