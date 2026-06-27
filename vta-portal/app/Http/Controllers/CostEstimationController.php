<?php

namespace App\Http\Controllers;

use App\Models\CostEstimation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostEstimationController extends Controller
{
    public function index(Request $request)
    {
        $query = CostEstimation::with(['patient', 'createdBy']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $costEstimations = $query->latest()->paginate(20);

        return view('cost-estimations.index', compact('costEstimations'));
    }

    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();

        return view('cost-estimations.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'          => 'required|exists:patients,id',
            'version_number'      => 'nullable|integer|min:1',
            'title'               => 'nullable|string|max:255',
            'estimated_amount'    => 'required|numeric|min:0',
            'estimated_sessions'  => 'nullable|integer|min:0',
            'estimated_duration'  => 'nullable|string|max:100',
            'sent_date'           => 'nullable|date',
            'sent_to'             => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'document_path'       => 'nullable|string|max:500',
        ]);

        $data['created_by'] = Auth::id();

        if (empty($data['version_number'])) {
            $lastVersion = CostEstimation::where('patient_id', $data['patient_id'])
                ->max('version_number');
            $data['version_number'] = ($lastVersion ?? 0) + 1;
        }

        CostEstimation::create($data);

        return redirect()->back()
            ->with('success', 'Cost estimation created successfully.');
    }

    public function show(CostEstimation $costEstimation)
    {
        $costEstimation->load(['patient', 'createdBy']);

        return view('cost-estimations.show', compact('costEstimation'));
    }

    public function edit(CostEstimation $costEstimation)
    {
        $patients = Patient::orderBy('first_name')->get();

        return view('cost-estimations.edit', compact('costEstimation', 'patients'));
    }

    public function update(Request $request, CostEstimation $costEstimation)
    {
        $data = $request->validate([
            'patient_id'          => 'required|exists:patients,id',
            'version_number'      => 'nullable|integer|min:1',
            'title'               => 'nullable|string|max:255',
            'estimated_amount'    => 'required|numeric|min:0',
            'estimated_sessions'  => 'nullable|integer|min:0',
            'estimated_duration'  => 'nullable|string|max:100',
            'sent_date'           => 'nullable|date',
            'sent_to'             => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'document_path'       => 'nullable|string|max:500',
        ]);

        $costEstimation->update($data);

        return redirect()->route('cost-estimations.show', $costEstimation)
            ->with('success', 'Cost estimation updated successfully.');
    }

    public function destroy(CostEstimation $costEstimation)
    {
        $costEstimation->delete();

        return redirect()->route('cost-estimations.index')
            ->with('success', 'Cost estimation deleted successfully.');
    }
}
