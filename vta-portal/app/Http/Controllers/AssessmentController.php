<?php
namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'fee_agreed_amount' => 'nullable|numeric|min:0',
            'fee_agreed_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'date_client_contacted' => 'nullable|date',
            'assessor' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'assessment_date' => 'nullable|date',
            'assessment_cost' => 'nullable|numeric|min:0',
            'assessment_cost_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();

        if ($request->hasFile('fee_agreed_document')) {
            $data['fee_agreed_document_path'] = $request->file('fee_agreed_document')->store('assessments', 'vta-documents');
        }

        if ($request->hasFile('assessment_cost_document')) {
            $data['assessment_cost_document_path'] = $request->file('assessment_cost_document')->store('assessments', 'vta-documents');
        }

        $assessment = $patient->assessment()->create($data);

        if ($patient->canTransitionTo('Assessment Scheduled')) {
            $patient->update(['status' => 'Assessment Scheduled']);
        }

        return redirect()->route('patients.show', $patient)->with('success', 'Assessment created.');
    }

    public function edit(Assessment $assessment): View
    {
        return view('assessments.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $data = $request->validate([
            'fee_agreed_amount' => 'nullable|numeric|min:0',
            'fee_agreed_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'date_client_contacted' => 'nullable|date',
            'assessor' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'assessment_date' => 'nullable|date',
            'assessment_cost' => 'nullable|numeric|min:0',
            'assessment_cost_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'report_sent' => 'nullable|boolean',
            'report_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('fee_agreed_document')) {
            $data['fee_agreed_document_path'] = $request->file('fee_agreed_document')->store('assessments', 'vta-documents');
        }

        if ($request->hasFile('assessment_cost_document')) {
            $data['assessment_cost_document_path'] = $request->file('assessment_cost_document')->store('assessments', 'vta-documents');
        }

        if ($request->hasFile('report_document')) {
            $data['report_document_path'] = $request->file('report_document')->store('assessments', 'vta-documents');
        }

        if ($request->boolean('report_sent') && !$assessment->report_document_path && !$request->hasFile('report_document')) {
            return back()->withErrors(['report_document' => 'A report document must be uploaded before marking as sent.'])->withInput();
        }

        $assessment->update($data);

        if ($request->boolean('report_sent')) {
            $patient = $assessment->patient;
            if ($patient->canTransitionTo('Report Sent')) {
                $patient->update(['status' => 'Report Sent']);
            }
        }

        return redirect()->route('patients.show', $assessment->patient)->with('success', 'Assessment updated.');
    }
}
