<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientAssociate;
use App\Models\Communication;
use App\Models\Document;
use App\Models\CostEstimation;
use App\Models\FundingCycle;
use App\Models\CaseManager;
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

        if ($request->has('needs_review')) {
            $query->where('needs_review', filter_var($request->needs_review, FILTER_VALIDATE_BOOLEAN));
        } else {
            $query->where('needs_review', true);
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

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);

        $data = $request->validate([
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'referral_date' => 'nullable|date',
            'first_contact_date' => 'nullable|date',
            'discharge_date' => 'nullable|date',
            'invoice_recipient_type' => 'nullable|in:Case Manager Company,Solicitor,Insurance Company,Other',
            'invoice_recipient_name' => 'nullable|string|max:255',
            'invoice_recipient_email' => 'nullable|email|max:255',
            'invoice_recipient_address' => 'nullable|string',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'folder_path' => 'nullable|string|max:255',
        ]);

        $data['referral_date'] = $data['referral_date'] ?? now()->toDateString();
        $data['created_by'] = Auth::id();

        $patient = Patient::create($data);

        return redirect()->route('patients.show', $patient);
    }

    public function show(Patient $patient)
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
            'communications' => function ($q) {
                $q->latest();
            },
            'caseNotes' => function ($q) {
                $q->with(['associate', 'signedOffBy'])->latest('session_date');
            },
        ]);

        $associates = \App\Models\Associate::where('is_active', true)->orderBy('name')->get();
        $documentTypes = \App\Models\DocumentType::where('is_active', true)->orderBy('sort_order')->get();

        return view('patients.show', compact('patient', 'associates', 'documentTypes'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'referral_date' => 'nullable|date',
            'first_contact_date' => 'nullable|date',
            'discharge_date' => 'nullable|date',
            'invoice_recipient_type' => 'nullable|in:Case Manager Company,Solicitor,Insurance Company,Other',
            'invoice_recipient_name' => 'nullable|string|max:255',
            'invoice_recipient_email' => 'nullable|email|max:255',
            'invoice_recipient_address' => 'nullable|string',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'folder_path' => 'nullable|string|max:255',
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

        $patient->update($data);

        return redirect()->back();
    }

    private function allowedTransitions(): array
    {
        return [
            'Enquiry Logged'            => ['Response Sent'],
            'Response Sent'             => ['Awaiting LOI', 'Enquiry Logged'],
            'Awaiting LOI'              => ['LOI Received', 'Response Sent'],
            'LOI Received'              => ['Assessment Scheduled'],
            'Assessment Scheduled'      => ['Assessment Completed', 'LOI Received'],
            'Assessment Completed'      => ['Report Drafted'],
            'Report Drafted'            => ['Report Sent', 'Assessment Completed'],
            'Report Sent'               => ['Cost Estimation Sent'],
            'Cost Estimation Sent'      => ['Awaiting Funding Approval'],
            'Awaiting Funding Approval' => ['Funding Approved', 'Cost Estimation Sent'],
            'Funding Approved'          => ['Treatment Active'],
            'Treatment Active'          => ['Awaiting Further Funding', 'Discharged'],
            'Awaiting Further Funding'  => ['Funding Approved'],
            'Discharged'                => ['Case Closed'],
            'Case Closed'               => [],
        ];
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

        return redirect()->back();
    }
}
