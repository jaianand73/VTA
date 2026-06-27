<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Associate;
use App\Models\ActivityType;
use App\Models\Communication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'associate', 'activityType', 'createdBy'])
            ->latest('scheduled_at')
            ->paginate(20);

        return view('appointments.index', compact('appointments'));
    }

    public function calendar()
    {
        $associates = Associate::where('is_active', true)->get();
        $activityTypes = ActivityType::where('is_active', true)->get();

        return view('appointments.calendar', compact('associates', 'activityTypes'));
    }

    public function fetchEvents(Request $request)
    {
        $query = Appointment::with(['patient', 'associate', 'activityType']);

        if ($request->filled('associate_id')) {
            $query->where('associate_id', $request->associate_id);
        }

        if ($request->filled('activity_type_id')) {
            $query->where('activity_type_id', $request->activity_type_id);
        }

        $appointments = $query->get();

        $colorMap = [
            'Scheduled' => '#3B82F6',
            'Completed' => '#10B981',
            'Cancelled' => '#EF4444',
            'DNA'       => '#F59E0B',
        ];

        $appointmentEvents = $appointments->map(function ($appt) use ($colorMap) {
            return [
                'id'          => $appt->id,
                'title'       => $appt->patient?->first_name . ' ' . $appt->patient?->last_name . ' - ' . $appt->associate?->name,
                'start'       => $appt->scheduled_at,
                'end'         => $appt->scheduled_at ? date('Y-m-d H:i:s', strtotime($appt->scheduled_at . ' + ' . ($appt->duration_minutes ?? 60) . ' minutes')) : null,
                'backgroundColor' => $colorMap[$appt->status] ?? '#6B7280',
                'borderColor' => $colorMap[$appt->status] ?? '#6B7280',
                'extendedProps' => [
                    'type'        => 'appointment',
                    'patient'     => $appt->patient?->first_name . ' ' . $appt->patient?->last_name,
                    'associate'   => $appt->associate?->name,
                    'activity'    => $appt->activityType?->name,
                    'location'    => $appt->location,
                    'status'      => $appt->status,
                    'notes'       => $appt->notes,
                    'duration'    => $appt->duration_minutes,
                    'travel_miles' => $appt->travel_miles,
                    'url'         => route('appointments.show', $appt->id),
                ],
            ];
        });

        $followUpEvents = $this->fetchFollowUpEvents();

        return response()->json($appointmentEvents->concat($followUpEvents)->values());
    }

    /**
     * Open follow-ups logged against an Enquiry, Patient, or Case Manager
     * (Communication rows with a follow_up_date and follow_up_completed = false).
     * These are overlaid on the same team calendar as a distinct event type —
     * not real Appointment records, so they vanish automatically once marked done.
     */
    private function fetchFollowUpEvents()
    {
        $followUps = Communication::whereNotNull('follow_up_date')
            ->where('follow_up_completed', false)
            ->with(['patient', 'caseManager.company', 'enquiry'])
            ->get();

        return $followUps->map(function ($comm) {
            $contact = null;
            $url = null;

            if ($comm->patient) {
                $contact = trim($comm->patient->first_name . ' ' . $comm->patient->last_name);
                $url = route('patients.show', $comm->patient_id);
            } elseif ($comm->caseManager) {
                $contact = trim($comm->caseManager->first_name . ' ' . $comm->caseManager->last_name);
                $url = route('companies.case-managers.show', [$comm->caseManager->company_id, $comm->case_manager_id]);
            } elseif ($comm->enquiry) {
                $contact = $comm->enquiry->enquirer_name;
                $url = route('enquiries.show', $comm->enquiry_id);
            }

            return [
                'id'              => 'followup-' . $comm->id,
                'title'           => 'Follow-up: ' . ($contact ?? $comm->subject ?? 'Untitled'),
                'start'           => $comm->follow_up_date->format('Y-m-d'),
                'allDay'          => true,
                'backgroundColor' => '#8B5CF6',
                'borderColor'     => '#8B5CF6',
                'extendedProps'   => [
                    'type'    => 'follow_up',
                    'contact' => $contact,
                    'subject' => $comm->subject,
                    'notes'   => $comm->summary,
                    'url'     => $url,
                    'completeUrl' => route('communications.complete-follow-up', $comm->id),
                ],
            ];
        });
    }

    public function create()
    {
        $patients = Patient::with('caseManager.company')->orderBy('first_name')->get();
        $associates = Associate::where('is_active', true)->get();
        $activityTypes = ActivityType::where('is_active', true)->get();

        return view('appointments.create', compact('patients', 'associates', 'activityTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'associate_id'     => 'required|exists:associates,id',
            'activity_type_id' => 'required|exists:activity_types,id',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'location'         => 'nullable|string|max:255',
            'status'           => 'nullable|string|max:50',
            'notes'            => 'nullable|string',
            'travel_miles'     => 'nullable|numeric|min:0',
        ]);

        $data['created_by'] = Auth::id();

        Appointment::create($data);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.caseManager.company', 'associate', 'activityType', 'createdBy']);

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('caseManager.company')->orderBy('first_name')->get();
        $associates = Associate::where('is_active', true)->get();
        $activityTypes = ActivityType::where('is_active', true)->get();

        return view('appointments.edit', compact('appointment', 'patients', 'associates', 'activityTypes'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'associate_id'     => 'required|exists:associates,id',
            'activity_type_id' => 'required|exists:activity_types,id',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'location'         => 'nullable|string|max:255',
            'status'           => 'nullable|string|max:50',
            'notes'            => 'nullable|string',
            'travel_miles'     => 'nullable|numeric|min:0',
        ]);

        $appointment->update($data);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
