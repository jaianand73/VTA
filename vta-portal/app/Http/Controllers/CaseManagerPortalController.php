<?php

namespace App\Http\Controllers;

use App\Models\CaseManager;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\CaseNote;
use App\Models\Document;
use App\Models\DocumentTypePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseManagerPortalController extends Controller
{
    protected function getCaseManager()
    {
        $caseManager = CaseManager::with('company')
            ->where('user_id', Auth::id())
            ->first();

        if (!$caseManager) {
            abort(403, 'No case manager profile linked to your account.');
        }

        return $caseManager;
    }

    public function index()
    {
        $caseManager = $this->getCaseManager();

        $patients = Patient::with(['assignedStaff', 'patientAssociates.associate'])
            ->where('case_manager_id', $caseManager->id)
            ->orderBy('referral_date', 'desc')
            ->get();

        $activePatients = $patients->whereNotIn('status', ['Discharged', 'Case Closed']);

        $upcomingAppointments = Appointment::whereIn('patient_id', $patients->pluck('id'))
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDays(14))
            ->with(['patient', 'associate', 'activityType'])
            ->orderBy('scheduled_at')
            ->get();

        return view('portal.case-manager.dashboard', compact(
            'caseManager', 'patients', 'activePatients', 'upcomingAppointments'
        ));
    }

    public function patient(Patient $patient)
    {
        $caseManager = $this->getCaseManager();

        $this->authorize('view', $patient);

        $patient->load(['caseManager.company', 'patientAssociates.associate']);

        $documents = Document::where('patient_id', $patient->id)
            ->with('documentType')
            ->get();

        $permittedDocTypes = DocumentTypePermission::where('role', 'case_manager')
            ->where('can_view', true)
            ->pluck('document_type_id');

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with(['associate', 'activityType'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $caseNotes = CaseNote::where('patient_id', $patient->id)
            ->with('associate')
            ->orderBy('session_date', 'desc')
            ->get();

        return view('portal.case-manager.patient', compact(
            'caseManager', 'patient', 'documents', 'permittedDocTypes',
            'appointments', 'caseNotes'
        ));
    }

    public function storeCaseNote(Request $request, Patient $patient)
    {
        $caseManager = $this->getCaseManager();

        $this->authorize('view', $patient);

        $data = $request->validate([
            'content'      => 'required|string',
            'session_date' => 'required|date',
            'note_type'    => 'required|string|max:50',
        ]);

        CaseNote::create([
            'patient_id'    => $patient->id,
            'associate_id'  => null,
            'session_date'  => $data['session_date'],
            'note_type'     => $data['note_type'],
            'content'       => $data['content'],
            'is_signed_off' => false,
        ]);

        return redirect()->route('case-manager-portal.patient', $patient)
            ->with('success', 'Case note added successfully.');
    }
}
