<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Associate;
use App\Models\Appointment;
use App\Models\CaseNote;
use App\Models\ActivityType;
use App\Models\Document;
use App\Models\Referral;
use App\Models\ReferralDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $activeReferralCount = Referral::where('associate_id', $associate->id)
            ->whereIn('status', ['Assessment', 'Proposal Submitted', 'Approved'])
            ->count();

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
            'associate', 'patientCount', 'activeReferralCount', 'upcomingAppointments', 'completedAppointments'
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

        $myInvoices = \App\Models\AssociateInvoice::where('associate_id', $associate->id)
            ->where('patient_id', $patient->id)
            ->orderBy('invoice_date', 'desc')
            ->get();

        return view('portal.associate.patient', compact(
            'associate', 'patient', 'appointments', 'caseNotes', 'permittedDocTypes', 'myInvoices'
        ));
    }

    public function uploadNote(Request $request)
    {
        $associate = $this->getAssociate();

        $data = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'session_date' => 'required|date',
            'note_type'    => 'required|string|max:100',
            'stage'        => 'nullable|string|in:Draft,Revision,Final',
            'needs_review' => 'nullable|boolean',
            'document'     => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
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
        $data['needs_review'] = $request->boolean('needs_review');

        $caseNote = CaseNote::create($data);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store("case-notes/{$caseNote->patient_id}", 'vta-documents');
            Document::create([
                'patient_id'         => $caseNote->patient_id,
                'document_type_id'   => null,
                'file_name'          => $file->getClientOriginalName(),
                'stored_file_name'   => basename($path),
                'file_path'          => $path,
                'is_password_protected' => false,
            ]);
        }

        return back()->with('success', 'Case note uploaded successfully.');
    }

    public function storeInvoice(Request $request)
    {
        $associate = $this->getAssociate();

        $data = $request->validate([
            'patient_id'         => 'required|exists:patients,id',
            'invoice_date'       => 'required|date',
            'sessions_completed' => 'nullable|integer|min:0',
            'session_amount'     => 'nullable|numeric|min:0',
            'travel_amount'      => 'nullable|numeric|min:0',
            'total_amount'       => 'required|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $isAssigned = $associate->patients()
            ->where('patient_id', $data['patient_id'])
            ->whereNull('end_date')
            ->exists();

        if (!$isAssigned) abort(403, 'You are not assigned to this patient.');

        $data['associate_id'] = $associate->id;
        $data['status']       = 'Submitted';

        \App\Models\AssociateInvoice::create($data);

        return back()->with('success', 'Invoice submitted for admin review.');
    }

    public function calendar()
    {
        $associate = $this->getAssociate();
        $activityTypes = ActivityType::where('is_active', true)->get();

        return view('portal.associate.calendar', compact('associate', 'activityTypes'));
    }

    public function referrals()
    {
        $associate = $this->getAssociate();

        $referrals = Referral::where('associate_id', $associate->id)
            ->whereIn('status', ['Assessment', 'Proposal Submitted', 'Approved'])
            ->with('enquiry')
            ->latest()
            ->get();

        return view('portal.associate.referrals', compact('associate', 'referrals'));
    }

    public function referral(Referral $referral)
    {
        $associate = $this->getAssociate();

        if ($referral->associate_id !== $associate->id) {
            abort(403, 'You are not assigned to this referral.');
        }

        $referral->load([
            'enquiry', 'company', 'caseManager',
            'sessions.createdBy', 'sessions.activityType',
            'bills.createdBy',
            'communications.createdBy',
            'documents' => fn($q) => $q->where('visible_to_associate', true),
        ]);

        $activityTypes = \App\Models\ActivityType::where('is_active', true)->orderBy('name')->get();

        return view('portal.associate.referral', compact('referral', 'associate', 'activityTypes'));
    }

    public function reuploadDocument(Request $request, Referral $referral, ReferralDocument $document)
    {
        $associate = $this->getAssociate();

        if ($referral->associate_id !== $associate->id) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:20480',
        ]);

        Storage::disk('public')->delete($document->file_path);

        $document->update([
            'file_path'          => $request->file('file')->store('referrals/documents', 'public'),
            'revision_requested' => false,
            'revision_notes'     => null,
        ]);

        return back()->with('success', 'Document re-uploaded. Samy will be notified.');
    }
}
