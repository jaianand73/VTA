<?php

namespace App\Http\Controllers;

use App\Models\FundingCycle;
use App\Models\Patient;
use App\Models\CostEstimation;
use App\Services\FundingBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FundingCycleController extends Controller
{
    public function index(Request $request)
    {
        $query = FundingCycle::with(['patient', 'costEstimation', 'createdBy']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $fundingCycles = $query->latest()->paginate(20);

        return view('funding-cycles.index', compact('fundingCycles'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $costEstimations = CostEstimation::with('patient')->get();

        $preselectedPatient = $request->filled('patient_id')
            ? Patient::find($request->patient_id)
            : null;

        $estimationsByPatient = CostEstimation::with('patient')->get()
            ->groupBy('patient_id')
            ->map(fn($rows) => $rows->map(fn($r) => [
                'id' => $r->id,
                'label' => 'v' . $r->version_number . ' — £' . number_format($r->estimated_amount, 0),
            ]));

        return view('funding-cycles.create', compact('patients', 'costEstimations', 'preselectedPatient', 'estimationsByPatient'));
    }

    public function store(Request $request, FundingBalanceService $balanceService)
    {
        $data = $request->validate([
            'patient_id'             => 'required|exists:patients,id',
            'cost_estimation_id'     => 'nullable|exists:cost_estimations,id',
            'cycle_number'           => 'nullable|integer|min:1',
            'approved_amount'        => 'required|numeric|min:0',
            'approved_sessions'      => 'nullable|integer|min:0',
            'approval_date'          => 'required|date',
            'approval_document'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'estimated_duration'     => 'nullable|string|max:100',
            'funder_name'            => 'nullable|string|max:255',
            'funder_reference'       => 'nullable|string|max:255',
            'is_active'              => 'boolean',
            'notes'                  => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();

        // BR-P6: funding cycles are always sequential — block if one is already active
        $hasActiveCycle = FundingCycle::where('patient_id', $data['patient_id'])
            ->where('is_active', true)
            ->exists();

        if ($hasActiveCycle && !empty($data['is_active'])) {
            return back()->withInput()->withErrors([
                'is_active' => 'This patient already has an active funding cycle. Mark the existing cycle as inactive before creating a new one.',
            ]);
        }

        if ($request->hasFile('approval_document')) {
            $data['approval_document_path'] = $request->file('approval_document')
                ->store('funding-cycles', 'vta-documents');
        }

        if (empty($data['cycle_number'])) {
            $lastCycle = FundingCycle::where('patient_id', $data['patient_id'])
                ->max('cycle_number');
            $data['cycle_number'] = ($lastCycle ?? 0) + 1;
        }

        $fundingCycle = FundingCycle::create($data);

        if (!empty($data['approval_document_path'])) {
            $patient = $fundingCycle->patient;
            if ($patient && $patient->canTransitionTo('Funding Approved')) {
                $patient->update(['status' => 'Funding Approved']);
            }
        }

        return redirect()->route('funding-cycles.show', $fundingCycle)
            ->with('success', 'Funding cycle created successfully.');
    }

    public function show(FundingCycle $fundingCycle)
    {
        $fundingCycle->load(['patient', 'costEstimation', 'createdBy', 'vtaInvoices']);
        $balanceService = app(FundingBalanceService::class);
        $remaining = $balanceService->remainingBalance($fundingCycle);
        $usagePercent = $balanceService->usagePercentage($fundingCycle);

        return view('funding-cycles.show', compact('fundingCycle', 'remaining', 'usagePercent'));
    }

    public function edit(FundingCycle $fundingCycle)
    {
        $patients = Patient::orderBy('first_name')->get();
        $costEstimations = CostEstimation::with('patient')->get();

        $estimationsByPatient = CostEstimation::with('patient')->get()
            ->groupBy('patient_id')
            ->map(fn($rows) => $rows->map(fn($r) => [
                'id' => $r->id,
                'label' => 'v' . $r->version_number . ' — £' . number_format($r->estimated_amount, 0),
            ]));

        return view('funding-cycles.edit', compact('fundingCycle', 'patients', 'costEstimations', 'estimationsByPatient'));
    }

    public function update(Request $request, FundingCycle $fundingCycle)
    {
        $data = $request->validate([
            'patient_id'             => 'required|exists:patients,id',
            'cost_estimation_id'     => 'nullable|exists:cost_estimations,id',
            'cycle_number'           => 'nullable|integer|min:1',
            'approved_amount'        => 'required|numeric|min:0',
            'approved_sessions'      => 'nullable|integer|min:0',
            'approval_date'          => 'required|date',
            'approval_document'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'estimated_duration'     => 'nullable|string|max:100',
            'funder_name'            => 'nullable|string|max:255',
            'funder_reference'       => 'nullable|string|max:255',
            'is_active'              => 'boolean',
            'notes'                  => 'nullable|string',
        ]);

        if ($request->hasFile('approval_document')) {
            $data['approval_document_path'] = $request->file('approval_document')
                ->store('funding-cycles', 'vta-documents');
        }

        $fundingCycle->update($data);

        if (!empty($data['approval_document_path'])) {
            $patient = $fundingCycle->patient;
            if ($patient && $patient->canTransitionTo('Funding Approved')) {
                $patient->update(['status' => 'Funding Approved']);
            }
        }

        return redirect()->route('funding-cycles.show', $fundingCycle)
            ->with('success', 'Funding cycle updated successfully.');
    }

    public function destroy(FundingCycle $fundingCycle)
    {
        $fundingCycle->delete();

        return redirect()->route('funding-cycles.index')
            ->with('success', 'Funding cycle deleted successfully.');
    }
}
