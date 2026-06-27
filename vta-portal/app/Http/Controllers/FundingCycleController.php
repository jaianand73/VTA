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

    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        $costEstimations = CostEstimation::with('patient')->get();

        return view('funding-cycles.create', compact('patients', 'costEstimations'));
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
            'approval_document_path' => 'nullable|string|max:500',
            'estimated_duration'     => 'nullable|string|max:100',
            'funder_name'            => 'nullable|string|max:255',
            'funder_reference'       => 'nullable|string|max:255',
            'is_active'              => 'boolean',
            'notes'                  => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();

        if (empty($data['cycle_number'])) {
            $lastCycle = FundingCycle::where('patient_id', $data['patient_id'])
                ->max('cycle_number');
            $data['cycle_number'] = ($lastCycle ?? 0) + 1;
        }

        $fundingCycle = FundingCycle::create($data);

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

        return view('funding-cycles.edit', compact('fundingCycle', 'patients', 'costEstimations'));
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
            'approval_document_path' => 'nullable|string|max:500',
            'estimated_duration'     => 'nullable|string|max:100',
            'funder_name'            => 'nullable|string|max:255',
            'funder_reference'       => 'nullable|string|max:255',
            'is_active'              => 'boolean',
            'notes'                  => 'nullable|string',
        ]);

        $fundingCycle->update($data);

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
