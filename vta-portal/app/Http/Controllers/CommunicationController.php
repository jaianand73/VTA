<?php

namespace App\Http\Controllers;

use App\Models\Communication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'enquiry_id' => 'nullable|exists:enquiries,id',
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'patient_id' => 'nullable|exists:patients,id',
            'type' => 'required|string|max:50',
            'direction' => 'required|string|max:10',
            'subject' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'communication_date' => 'nullable|date',
            'follow_up_date' => 'nullable|date',
            'follow_up_completed' => 'boolean',
        ]);

        $data['communication_date'] = $data['communication_date'] ?? now();
        $data['created_by'] = Auth::id();

        Communication::create($data);

        return redirect()->back();
    }

    public function update(Request $request, Communication $communication)
    {
        $data = $request->validate([
            'enquiry_id' => 'nullable|exists:enquiries,id',
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'patient_id' => 'nullable|exists:patients,id',
            'type' => 'required|string|max:50',
            'direction' => 'required|string|max:10',
            'subject' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'communication_date' => 'nullable|date',
            'follow_up_date' => 'nullable|date',
            'follow_up_completed' => 'boolean',
        ]);

        $communication->update($data);

        return redirect()->back();
    }

    public function destroy(Communication $communication)
    {
        $communication->delete();

        return redirect()->back();
    }

    public function completeFollowUp(Communication $communication)
    {
        $communication->update(['follow_up_completed' => true]);

        return redirect()->back()->with('success', 'Follow-up marked as done.');
    }
}
