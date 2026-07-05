<?php

namespace App\Http\Controllers;

use App\Models\Associate;
use App\Models\CaseManager;
use App\Models\Company;
use App\Models\Enquiry;
use App\Models\Patient;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = Referral::with('caseManager', 'company', 'associate');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_first_name', 'like', "%{$search}%")
                  ->orWhere('patient_last_name', 'like', "%{$search}%")
                  ->orWhere('referral_ref', 'like', "%{$search}%")
                  ->orWhere('patient_postcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $referrals = $query->latest()->paginate(20);

        return view('referrals.index', compact('referrals'));
    }

    public function create(Request $request)
    {
        if (!$request->filled('enquiry_id')) {
            return redirect()->route('enquiries.index')
                ->with('info', 'Referrals must be created from an enquiry. Use the "Promote to Referral" button on the enquiry.');
        }

        $companies   = Company::orderBy('name')->get();
        $caseManagers = CaseManager::with('company')->orderBy('first_name')->get();
        $associates  = Associate::where('is_active', true)->orderBy('name')->get();
        $enquiry     = Enquiry::find($request->enquiry_id);

        return view('referrals.create', compact('companies', 'caseManagers', 'associates', 'enquiry'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'referral_ref'            => 'required|string|max:50|unique:referrals,referral_ref',
            'enquiry_id'              => 'nullable|exists:enquiries,id',
            'patient_first_name'      => 'required|string|max:100',
            'patient_last_name'       => 'required|string|max:100',
            'patient_dob'             => 'nullable|date',
            'patient_address'         => 'nullable|string|max:500',
            'patient_postcode'        => 'required|string|max:20',
            'patient_phone'           => 'nullable|string|max:50',
            'patient_email'           => 'nullable|email|max:255',
            'company_id'              => 'nullable|exists:companies,id',
            'case_manager_id'         => 'required|exists:case_managers,id',
            'special_instructions'    => 'nullable|string',
            'notes'                   => 'nullable|string',
            'status'                  => 'nullable|string|max:50',
        ]);

        $data['created_by'] = Auth::id();

        $referral = Referral::create($data);

        // If promoted from an enquiry, mark the enquiry as converted
        if ($referral->enquiry_id) {
            Enquiry::where('id', $referral->enquiry_id)
                ->update(['status' => 'Converted to Referral']);
        }

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'Referral created successfully.');
    }

    public function show(Referral $referral)
    {
        $referral->load([
            'enquiry', 'company', 'caseManager', 'associate', 'patient', 'createdBy',
            'sessions.createdBy', 'sessions.activityType',
            'bills.createdBy',
            'communications.createdBy',
            'documents.uploadedBy',
        ]);
        $companies     = Company::orderBy('name')->get();
        $caseManagers  = CaseManager::with('company')->orderBy('first_name')->get();
        $associates    = Associate::where('is_active', true)->orderBy('name')->get();
        $activityTypes = \App\Models\ActivityType::where('is_active', true)->orderBy('name')->get();

        return view('referrals.show', compact('referral', 'companies', 'caseManagers', 'associates', 'activityTypes'));
    }

    public function update(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'referral_ref'            => 'nullable|string|max:50|unique:referrals,referral_ref,' . $referral->id,
            'patient_first_name'      => 'required|string|max:100',
            'patient_last_name'       => 'required|string|max:100',
            'patient_dob'             => 'nullable|date',
            'patient_address'         => 'nullable|string|max:500',
            'patient_postcode'        => 'nullable|string|max:20',
            'patient_phone'           => 'nullable|string|max:50',
            'patient_email'           => 'nullable|email|max:255',
            'company_id'              => 'nullable|exists:companies,id',
            'case_manager_id'         => 'nullable|exists:case_managers,id',
            'associate_id'            => 'nullable|exists:associates,id',
            'special_instructions'    => 'nullable|string',
            'visit_approved_date'     => 'nullable|date',
            'proposal_submitted_date' => 'nullable|date',
            'proposal_approved_date'  => 'nullable|date',
            'status'                  => 'nullable|string|max:50',
            'notes'                   => 'nullable|string',
        ]);

        $referral->update($data);

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'Referral updated.');
    }

    public function destroy(Referral $referral)
    {
        $referral->delete();
        return redirect()->route('referrals.index')
            ->with('success', 'Referral deleted.');
    }

    // Promote referral to patient
    public function convertToPatient(Referral $referral)
    {
        if ($referral->patient) {
            return redirect()->route('referrals.show', $referral)
                ->with('error', 'This referral has already been converted to a patient.');
        }

        $companies    = Company::orderBy('name')->get();
        $caseManagers = CaseManager::with('company')->orderBy('first_name')->get();
        $associates   = Associate::where('is_active', true)->orderBy('name')->get();

        return view('referrals.convert', compact('referral', 'companies', 'caseManagers', 'associates'));
    }

    // POST: actually create the patient from the conversion form
    public function storePatient(Request $request, Referral $referral)
    {
        if ($referral->patient) {
            return redirect()->route('referrals.show', $referral)
                ->with('error', 'This referral has already been converted to a patient.');
        }

        $data = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'dob'          => 'nullable|date',
            'postcode'     => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'patient_ref'  => 'nullable|string|max:50|unique:patients,patient_ref',
            'associate_id' => 'nullable|exists:associates,id',
        ]);

        $patient = \App\Models\Patient::create([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'dob'             => $data['dob'] ?? null,
            'postcode'        => $data['postcode'] ?? null,
            'address'         => $data['address'] ?? null,
            'phone'           => $data['phone'] ?? null,
            'email'           => $data['email'] ?? null,
            'patient_ref'     => $data['patient_ref'] ?? null,
            'enquiry_id'      => $referral->enquiry_id,
            'referral_id'     => $referral->id,
            'company_id'      => $referral->company_id,
            'case_manager_id' => $referral->case_manager_id,
            'associate_id'    => $data['associate_id'] ?? $referral->associate_id,
            'status'          => 'Treatment Active',
            'created_by'      => auth()->id(),
        ]);

        $referral->update(['status' => 'Approved']);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient record created successfully.');
    }

    // Update go-ahead to visit
    public function approveVisit(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'visit_approved_date'     => 'required|date',
            'associate_id'            => 'required|exists:associates,id',
            'visit_approved_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        if ($request->hasFile('visit_approved_document')) {
            $path = $request->file('visit_approved_document')
                ->store('referrals/visit-approvals', 'public');
            $data['visit_approved_document'] = $path;
        }

        $referral->update($data + ['status' => 'Assessment']);

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'Go-ahead to Visit recorded.');
    }

    // Submit proposal
    public function submitProposal(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'proposal_submitted_date' => 'required|date',
            'proposal_document'       => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('proposal_document')) {
            $path = $request->file('proposal_document')
                ->store('referrals/proposals', 'public');
            $data['proposal_document'] = $path;
        }

        $referral->update($data + ['status' => 'Proposal Submitted']);

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'Proposal submission recorded.');
    }

    // Record proposal approval — moves status to Approved
    public function approveProposal(Request $request, Referral $referral)
    {
        $request->validate([
            'proposal_approved_date' => 'required|date',
        ]);

        $referral->update([
            'proposal_approved_date' => $request->proposal_approved_date,
            'status'                 => 'Approved',
        ]);

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'Proposal approved. Referral is now ready to convert to a patient.');
    }
}
