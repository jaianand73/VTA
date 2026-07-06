<?php

namespace App\Http\Controllers;

use App\Models\Associate;
use App\Models\Enquiry;
use App\Models\Company;
use App\Models\CaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('enquirer_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enquiries = $query->latest()->paginate(20);

        return view('enquiries.index', compact('enquiries'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $caseManagers = CaseManager::with('company')->orderBy('first_name')->get();
        $associates = Associate::where('is_active', true)->orderBy('name')->get();
        return view('enquiries.create', compact('companies', 'caseManagers', 'associates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'enquiry_ref' => 'nullable|string|max:50',
            'enquirer_name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'nullable|in:Email,LinkedIn,Phone,Referral Letter,Website,Word of Mouth,Other',
            'reason' => 'nullable|string',
            'client_location' => 'nullable|string|max:255',
            'nearest_associate_id' => 'nullable|exists:associates,id',
            'enquiry_date' => 'nullable|date',
            'first_response_date' => 'nullable|date',
            'first_response_remarks' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'initial_assessment_approved' => 'nullable|boolean',
            'initial_assessment_reason' => 'nullable|string',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts.*.role|string|max:255',
            'contacts.*.role' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:50',
            'communications' => 'nullable|array',
            'communications.*.type' => 'required_with:communications.*.subject|string|max:50',
            'communications.*.direction' => 'nullable|string|max:10',
            'communications.*.subject' => 'nullable|string|max:255',
            'communications.*.summary' => 'nullable|string',
            'communications.*.communication_date' => 'nullable|date',
        ]);

        if ($data['company_id'] ?? null) {
            $company = Company::find($data['company_id']);
            $data['company_name'] = $company->name;
        }

        $data['source'] = $data['source'] ?? 'Other';
        $data['enquiry_date'] = $data['enquiry_date'] ?? now()->toDateString();
        $data['created_by'] = Auth::id();

        $enquiry = Enquiry::create($data);

        foreach ($request->contacts ?? [] as $contact) {
            if (!empty($contact['name'])) {
                $enquiry->contacts()->create($contact);
            }
        }

        foreach ($request->communications ?? [] as $comm) {
            if (!empty($comm['subject']) || !empty($comm['summary'])) {
                \App\Models\Communication::create([
                    'enquiry_id' => $enquiry->id,
                    'case_manager_id' => $enquiry->case_manager_id,
                    'type' => $comm['type'] ?? 'Other',
                    'direction' => $comm['direction'] ?? 'Outbound',
                    'subject' => $comm['subject'] ?? ($comm['type'] ?? 'Note'),
                    'summary' => $comm['summary'] ?? null,
                    'communication_date' => $comm['communication_date'] ?? now(),
                    'created_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('enquiries.show', $enquiry)->with('just_created', true);
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load('createdBy', 'company', 'selectedCompany', 'selectedCaseManager', 'caseManager', 'contacts', 'nearestAssociate', 'referral');

        $communications = \App\Models\Communication::where('enquiry_id', $enquiry->id)
            ->latest('communication_date')
            ->get();

        $documents = \App\Models\Document::where('enquiry_id', $enquiry->id)
            ->latest()
            ->get();

        $companies = Company::orderBy('name')->get();
        $caseManagers = CaseManager::with('company')->orderBy('first_name')->get();

        // Q35 — suggest associates whose region matches the enquiry's company city/location
        $location = $enquiry->selectedCompany?->city ?? $enquiry->selectedCompany?->name ?? '';
        $nearestAssociates = \App\Models\Associate::where('is_active', true)
            ->when($location, fn ($q) => $q->where(function ($inner) use ($location) {
                $inner->whereRaw('LOWER(?) LIKE CONCAT("%", LOWER(region), "%")', [$location])
                      ->orWhereRaw('LOWER(region) LIKE CONCAT("%", LOWER(?), "%")', [$location]);
            }))
            ->orderBy('name')
            ->get();

        $associates = Associate::where('is_active', true)->orderBy('name')->get();
        $documentTypes = \App\Models\DocumentType::orderBy('name')->get();

        return view('enquiries.show', compact('enquiry', 'companies', 'caseManagers', 'communications', 'documents', 'nearestAssociates', 'associates', 'documentTypes'));
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'enquiry_ref' => 'nullable|string|max:50',
            'enquirer_name' => 'required|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'nullable|in:Email,LinkedIn,Phone,Referral Letter,Website,Word of Mouth,Other',
            'reason' => 'nullable|string',
            'client_location' => 'nullable|string|max:255',
            'nearest_associate_id' => 'nullable|exists:associates,id',
            'enquiry_date' => 'nullable|date',
            'first_response_date' => 'nullable|date',
            'first_response_remarks' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'initial_assessment_approved' => 'nullable|boolean',
            'initial_assessment_reason' => 'nullable|string',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts.*.role|string|max:255',
            'contacts.*.role' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:50',
        ]);

        if ($data['company_id'] ?? null) {
            $company = Company::find($data['company_id']);
            $data['company_name'] = $company->name;
        } else {
            $data['company_name'] = null;
        }

        $data['source'] = $data['source'] ?? $enquiry->source;
        $data['enquiry_date'] = $data['enquiry_date'] ?? $enquiry->enquiry_date;

        $enquiry->update($data);

        $enquiry->contacts()->delete();
        foreach ($request->contacts ?? [] as $contact) {
            if (!empty($contact['name'])) {
                $enquiry->contacts()->create($contact);
            }
        }

        return redirect()->route('enquiries.show', $enquiry);
    }

    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $name = $enquiry->enquirer_name;
        $enquiry->delete();

        return redirect()->route('enquiries.index')
            ->with('success', "Enquiry for \"{$name}\" has been deleted.");
    }

    public function promoteToReferral(Enquiry $enquiry)
    {
        if ($enquiry->status === 'Not Proceeding') {
            return redirect()->route('enquiries.show', $enquiry)
                ->with('error', 'This enquiry is marked Not Proceeding and cannot be promoted to a referral.');
        }

        if ($enquiry->referral()->exists()) {
            return redirect()->route('referrals.show', $enquiry->referral)
                ->with('info', 'This enquiry has already been promoted to a referral.');
        }

        return redirect()->route('referrals.create', ['enquiry_id' => $enquiry->id]);
    }
}
