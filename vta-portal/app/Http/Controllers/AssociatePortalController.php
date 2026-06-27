<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Associate;
use App\Models\Appointment;
use App\Models\CaseNote;
use App\Models\ActivityType;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssociatePortalController extends Controller
{
    protected function getAssociate()
    {
        $associate = Associate::where('user_id', Auth::id())->first();

        if (!$associate) {
            abort(403, 'No associate profile linked to your account.');
        }

        return $associate;
    }

    public function index()
    {
        $associate = $this->getAssociate();

        $patientCount = $associate->patients()->count();

        $upcomingAppointments = Appointment::where('associate_id', $associate->id)
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDays(14))
            ->with(['patient', 'activityType'])
            ->orderBy('scheduled_at')
            ->get();

        $completedAppointments = Appointment::where('associate_id', $associate->id)
            ->where('status', 'Completed')
            ->whereDoesntHave('caseNotes')
            ->with(['patient'])
            ->get();

        return view('portal.associate.dashboard', compact(
            'associate', 'patientCount', 'upcomingAppointments', 'completedAppointments'
        ));
    }

    public function patient(Patient $patient)
    {
        $associate = $this->getAssociate();

        $this->authorize('view', $patient);

        $patient->load(['caseManager.company', 'documents.documentType', 'patientAssociates']);

        $appointments = Appointment::where('patient_id', $patient->id)
            ->where('associate_id', $associate->id)
            ->orderBy('scheduled_at')
            ->get();

        $caseNotes = CaseNote::where('patient_id', $patient->id)
            ->where('associate_id', $associate->id)
            ->orderBy('session_date', 'desc')
            ->get();

        $permittedDocTypes = \App\Models\DocumentTypePermission::where('role', 'associate')
            ->where('can_view', true)
            ->pluck('document_type_id');

        return view('portal.associate.patient', compact(
            'associate', 'patient', 'appointments', 'caseNotes', 'permittedDocTypes'
        ));
    }

    public function uploadNote(Request $request)
    {
        $associate = $this->getAssociate();

        $data = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'session_date' => 'required|date',
            'note_type'    => 'required|string|max:50',
            'content'      => 'nullable|string',
        ]);

        $isAssigned = $associate->patients()
            ->where('patient_id', $data['patient_id'])
            ->whereNull('end_date')
            ->exists();

        if (!$isAssigned) {
            return back()->withErrors(['patient_id' => 'You are not assigned to this patient.']);
        }

        $data['associate_id'] = $associate->id;
        $data['is_signed_off'] = false;

        CaseNote::create($data);

        return back()->with('success', 'Case note uploaded successfully.');
    }

    public function calendar()
    {
        $associate = $this->getAssociate();
        $activityTypes = ActivityType::where('is_active', true)->get();

        return view('portal.associate.calendar', compact('associate', 'activityTypes'));
    }
}
