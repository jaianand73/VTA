<?php

namespace App\Http\Controllers;

use App\Models\CaseNote;
use App\Models\Patient;
use App\Models\Associate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseNote::with(['patient', 'associate', 'signedOffBy']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('associate_id')) {
            $query->where('associate_id', $request->associate_id);
        }

        if ($request->filled('note_type')) {
            $query->where('note_type', $request->note_type);
        }

        $caseNotes = $query->latest('session_date')->paginate(20);

        return view('case-notes.index', compact('caseNotes'));
    }

    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        $associates = Associate::where('is_active', true)->get();

        return view('case-notes.create', compact('patients', 'associates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'associate_id'   => 'required|exists:associates,id',
            'session_date'   => 'required|date',
            'note_type'      => 'required|string|max:50',
            'content'        => 'nullable|string',
            'document_path'  => 'nullable|string|max:500',
        ]);

        $data['is_signed_off'] = false;

        CaseNote::create($data);

        return redirect()->route('case-notes.index')
            ->with('success', 'Case note created successfully.');
    }

    public function show(CaseNote $caseNote)
    {
        $caseNote->load(['patient', 'appointment', 'associate', 'signedOffBy']);

        return view('case-notes.show', compact('caseNote'));
    }

    public function edit(CaseNote $caseNote)
    {
        $patients = Patient::orderBy('first_name')->get();
        $associates = Associate::where('is_active', true)->get();

        return view('case-notes.edit', compact('caseNote', 'patients', 'associates'));
    }

    public function update(Request $request, CaseNote $caseNote)
    {
        $data = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'associate_id'   => 'required|exists:associates,id',
            'session_date'   => 'required|date',
            'note_type'      => 'required|string|max:50',
            'content'        => 'nullable|string',
            'document_path'  => 'nullable|string|max:500',
        ]);

        $caseNote->update($data);

        return redirect()->route('case-notes.show', $caseNote)
            ->with('success', 'Case note updated successfully.');
    }

    public function destroy(CaseNote $caseNote)
    {
        $caseNote->delete();

        return redirect()->route('case-notes.index')
            ->with('success', 'Case note deleted successfully.');
    }

    public function signOff(CaseNote $caseNote)
    {
        if ($caseNote->is_signed_off) {
            return back()->with('error', 'Case note is already signed off.');
        }

        $caseNote->update([
            'is_signed_off'  => true,
            'signed_off_by'  => Auth::id(),
            'signed_off_at'  => now(),
        ]);

        return back()->with('success', 'Case note signed off successfully.');
    }

    public function sendFeedback(Request $request, CaseNote $caseNote)
    {
        $data = $request->validate([
            'review_feedback' => 'required|string|max:2000',
        ]);

        $caseNote->update([
            'review_feedback' => $data['review_feedback'],
            'needs_review'    => true,
            'reviewed_by'     => Auth::id(),
            'reviewed_at'     => now(),
            'is_signed_off'   => false,
        ]);

        return back()->with('success', 'Revision feedback sent.');
    }
}
