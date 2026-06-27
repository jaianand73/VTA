<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use App\Models\Associate;
use App\Models\Company;
use App\Models\DocumentType;
use App\Models\DocumentTypePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'activity-types');

        $activityTypes = ActivityType::orderBy('sort_order')->orderBy('name')->get();
        $documentTypes = DocumentType::orderBy('sort_order')->orderBy('name')->get();
        $roles = ['case_manager', 'associate', 'patient'];
        $permissions = DocumentTypePermission::with('documentType')->get()->keyBy(fn($p) => $p->document_type_id . '-' . $p->role);
        $associates = Associate::with('user')->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('settings.index', compact(
            'tab', 'activityTypes', 'documentTypes', 'roles', 'permissions',
            'associates', 'companies', 'users'
        ));
    }

    public function storeActivityType(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        ActivityType::create($data);

        return redirect()->route('settings.index', ['tab' => 'activity-types'])
            ->with('success', 'Activity type created.');
    }

    public function updateActivityType(Request $request, ActivityType $activityType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $activityType->update($data);

        return redirect()->route('settings.index', ['tab' => 'activity-types'])
            ->with('success', 'Activity type updated.');
    }

    public function destroyActivityType(ActivityType $activityType)
    {
        $activityType->delete();

        return redirect()->route('settings.index', ['tab' => 'activity-types'])
            ->with('success', 'Activity type deleted.');
    }

    public function storeDocumentType(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $docType = DocumentType::create($data);

        foreach (['case_manager', 'associate', 'patient'] as $role) {
            DocumentTypePermission::create([
                'document_type_id' => $docType->id,
                'role' => $role,
                'can_view' => false,
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('settings.index', ['tab' => 'document-types'])
            ->with('success', 'Document type created.');
    }

    public function updateDocumentType(Request $request, DocumentType $documentType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $documentType->update($data);

        return redirect()->route('settings.index', ['tab' => 'document-types'])
            ->with('success', 'Document type updated.');
    }

    public function destroyDocumentType(DocumentType $documentType)
    {
        $documentType->delete();

        return redirect()->route('settings.index', ['tab' => 'document-types'])
            ->with('success', 'Document type deleted.');
    }

    public function updatePermissions(Request $request)
    {
        $permissions = $request->input('permissions', []);

        foreach ($permissions as $key => $value) {
            [$documentTypeId, $role] = explode('-', $key);
            DocumentTypePermission::updateOrCreate(
                ['document_type_id' => $documentTypeId, 'role' => $role],
                ['can_view' => filter_var($value, FILTER_VALIDATE_BOOLEAN), 'updated_by' => auth()->id()]
            );
        }

        return redirect()->route('settings.index', ['tab' => 'document-permissions'])
            ->with('success', 'Document permissions updated.');
    }

    public function storeAssociate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'region' => 'required|string|max:255',
            'speciality' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'session_rate' => 'nullable|numeric|min:0',
            'travel_rate_per_mile' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Associate::create($data);

        return redirect()->route('settings.index', ['tab' => 'associates'])
            ->with('success', 'Associate created.');
    }

    public function updateAssociate(Request $request, Associate $associate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'region' => 'required|string|max:255',
            'speciality' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'session_rate' => 'nullable|numeric|min:0',
            'travel_rate_per_mile' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $associate->update($data);

        return redirect()->route('settings.index', ['tab' => 'associates'])
            ->with('success', 'Associate updated.');
    }

    public function createAssociateLogin(Request $request, Associate $associate)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $associate->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'associate',
            'is_active' => true,
        ]);

        $associate->update(['user_id' => $user->id, 'email' => $request->email]);

        return redirect()->route('settings.index', ['tab' => 'associates'])
            ->with('success', "Portal login created for {$associate->name}.");
    }

    public function storeCompany(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:Case Management,Law Firm,Solicitor,Insurance,Individual,Other',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $data['type'] = $data['type'] ?? 'Case Management';
        $data['status'] = 'Active';
        $data['created_by'] = Auth::id();

        $company = Company::create($data);

        if ($request->wantsJson()) {
            return response()->json(['id' => $company->id, 'name' => $company->name]);
        }

        return redirect()->back()->with('success', 'Company created.');
    }

    public function updateCompany(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:Case Management,Law Firm,Solicitor,Insurance,Individual,Other',
            'status' => 'nullable|in:Enquiry,Active,Inactive',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $data['type'] = $data['type'] ?? $company->type;
        $data['status'] = $data['status'] ?? $company->status;

        $company->update($data);

        return redirect()->route('settings.index', ['tab' => 'companies'])
            ->with('success', 'Company updated.');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,staff,associate',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:50',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;
        User::create($data);

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', 'User created.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,staff,associate',
            'is_active' => 'boolean',
            'phone' => 'nullable|string|max:50',
        ]);

        $user->update($data);

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', 'User updated.');
    }

    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('settings.index', ['tab' => 'users'])
            ->with('success', "Password reset for {$user->name}.");
    }
}
