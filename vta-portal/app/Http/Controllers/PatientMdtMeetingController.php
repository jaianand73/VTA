<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientMdtMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientMdtMeetingController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'meeting_date' => 'required|date',
            'attendees'    => 'nullable|string|max:500',
            'discussion'   => 'required|string',
            'outcomes'     => 'nullable|string',
        ]);

        $data['patient_id'] = $patient->id;
        $data['created_by'] = Auth::id();

        PatientMdtMeeting::create($data);

        return redirect()->back()->with('success', 'MDT meeting recorded.');
    }

    public function destroy(Patient $patient, PatientMdtMeeting $meeting)
    {
        $meeting->delete();
        return redirect()->back()->with('success', 'MDT meeting deleted.');
    }
}
