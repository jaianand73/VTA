<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CaseManager;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $companies = $query->withCount('caseManagers')->latest()->paginate(20);

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:Case Management,Law Firm,Solicitor,Insurance,Individual,Other',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'nullable|string|max:50',
            'first_contact_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $data['type'] = $data['type'] ?? 'Case Management';
        $data['created_by'] = Auth::id();

        $company = Company::create($data);

        return redirect()->route('companies.show', $company);
    }

    public function show(Company $company)
    {
        $company->load([
            'caseManagers' => function ($q) {
                $q->withCount('patients');
            },
            'enquiries',
            'createdBy',
        ]);

        $patientIds = CaseManager::where('company_id', $company->id)->pluck('id');
        $patients = Patient::whereIn('case_manager_id', $patientIds)->get();
        $communications = collect(); // Communications belong to case managers, not directly to companies

        return view('companies.show', compact('company', 'patients'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:Case Management,Law Firm,Solicitor,Insurance,Individual,Other',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'nullable|string|max:50',
            'first_contact_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $data['type'] = $data['type'] ?? 'Case Management';

        $company->update($data);

        return redirect()->route('companies.show', $company);
    }

    public function destroy(Company $company)
    {
        $cmCount = $company->caseManagers()->count();
        $enquiryCount = $company->enquiries()->count();

        if ($cmCount > 0 || $enquiryCount > 0) {
            return redirect()->route('companies.show', $company)
                ->with('error', "Cannot delete \"{$company->name}\" — it has {$cmCount} case manager(s) and {$enquiryCount} enquiry(s) attached. Remove these first.");
        }

        $name = $company->name;
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', "Company \"{$name}\" has been deleted.");
    }
}
