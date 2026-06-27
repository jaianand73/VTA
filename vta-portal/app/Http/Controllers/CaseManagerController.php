<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CaseManager;
use App\Models\Patient;
use App\Models\Communication;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CaseManagerController extends Controller
{
    public function show(Company $company, CaseManager $caseManager)
    {
        $caseManager->load([
            'company',
            'user',
            'createdBy',
            'patients',
            'communications' => function ($q) {
                $q->latest();
            },
            'documents',
        ]);

        return view('case-managers.show', compact('company', 'caseManager'));
    }

    public function createPortalLogin(Request $request, Company $company, CaseManager $caseManager)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $caseManager->first_name . ' ' . $caseManager->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'case_manager',
            'is_active' => true,
        ]);

        $caseManager->update([
            'user_id' => $user->id,
            'email' => $request->email,
        ]);

        return redirect()->route('companies.case-managers.show', [$company, $caseManager])
            ->with('success', "Portal login created for {$caseManager->first_name} {$caseManager->last_name}.");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        $data['created_by'] = Auth::id();
        $caseManager = CaseManager::create($data);
        $caseManager->load('company');

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $caseManager->id,
                'name' => $caseManager->first_name . ' ' . $caseManager->last_name,
                'company_name' => $caseManager->company?->name,
            ]);
        }

        return redirect()->back()->with('success', 'Case manager created.');
    }

    public function update(Request $request, Company $company, CaseManager $caseManager)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
        ]);

        $caseManager->update($data);

        return redirect()->route('companies.show', $company)
            ->with('success', "{$caseManager->first_name} {$caseManager->last_name} updated.");
    }

    public function destroy(Company $company, CaseManager $caseManager)
    {
        $name = $caseManager->first_name . ' ' . $caseManager->last_name;
        $caseManager->delete();

        return redirect()->route('companies.show', $company)
            ->with('success', "{$name} removed.");
    }

    public function markNdaSigned(Company $company, CaseManager $caseManager)
    {
        $caseManager->update([
            'nda_signed' => true,
            'nda_signed_date' => now(),
        ]);

        return redirect()->route('companies.case-managers.show', [$company, $caseManager])
            ->with('success', "NDA marked as signed for {$caseManager->first_name} {$caseManager->last_name}.");
    }

    public function markMaterialsSent(Company $company, CaseManager $caseManager)
    {
        $caseManager->update([
            'materials_sent' => true,
            'materials_sent_date' => now(),
        ]);

        return redirect()->route('companies.case-managers.show', [$company, $caseManager])
            ->with('success', "Materials marked as sent for {$caseManager->first_name} {$caseManager->last_name}.");
    }
}
