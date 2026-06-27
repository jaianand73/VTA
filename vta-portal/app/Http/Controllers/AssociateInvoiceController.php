<?php

namespace App\Http\Controllers;

use App\Models\AssociateInvoice;
use App\Models\Associate;
use App\Models\Patient;
use App\Models\FundingCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssociateInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = AssociateInvoice::with(['associate', 'patient', 'fundingCycle', 'loggedBy']);

        if ($request->filled('associate_id')) {
            $query->where('associate_id', $request->associate_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $associateInvoices = $query->latest()->paginate(20);

        $summary = [
            'received_this_month' => AssociateInvoice::whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)->sum('total_amount'),
            'paid_this_month' => AssociateInvoice::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)->where('status', 'Paid')->sum('total_amount'),
            'overdue_count' => AssociateInvoice::whereIn('status', ['Received', 'Verified'])
                ->where('due_date', '<', now())->count(),
        ];

        $associates = Associate::where('is_active', true)->get();

        return view('associate-invoices.index', compact('associateInvoices', 'summary', 'associates'));
    }

    public function create()
    {
        $associates = Associate::where('is_active', true)->get();
        $patients = Patient::orderBy('first_name')->get();
        $fundingCycles = FundingCycle::with('patient')->where('is_active', true)->get();

        return view('associate-invoices.create', compact('associates', 'patients', 'fundingCycles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'associate_id'         => 'required|exists:associates,id',
            'patient_id'           => 'required|exists:patients,id',
            'funding_cycle_id'     => 'nullable|exists:funding_cycles,id',
            'invoice_reference'    => 'nullable|string|max:255',
            'invoice_date'         => 'required|date',
            'sessions_completed'   => 'nullable|integer|min:0',
            'travel_miles'         => 'nullable|numeric|min:0',
            'session_amount'       => 'nullable|numeric|min:0',
            'travel_amount'        => 'nullable|numeric|min:0',
            'total_amount'         => 'required|numeric|min:0',
            'notes'                => 'nullable|string',
            'document_path'        => 'nullable|string|max:500',
        ]);

        $data['logged_by'] = Auth::id();

        if (empty($data['due_date'])) {
            $data['due_date'] = \Carbon\Carbon::parse($data['invoice_date'])
                ->addDays(28)->format('Y-m-d');
        }

        AssociateInvoice::create($data);

        return redirect()->route('associate-invoices.index')
            ->with('success', 'Associate invoice logged successfully.');
    }

    public function show(AssociateInvoice $associateInvoice)
    {
        $associateInvoice->load(['associate', 'patient', 'fundingCycle', 'loggedBy']);

        return view('associate-invoices.show', compact('associateInvoice'));
    }

    public function edit(AssociateInvoice $associateInvoice)
    {
        $associates = Associate::where('is_active', true)->get();
        $patients = Patient::orderBy('first_name')->get();
        $fundingCycles = FundingCycle::with('patient')->where('is_active', true)->get();

        return view('associate-invoices.edit', compact('associateInvoice', 'associates', 'patients', 'fundingCycles'));
    }

    public function update(Request $request, AssociateInvoice $associateInvoice)
    {
        $data = $request->validate([
            'associate_id'         => 'required|exists:associates,id',
            'patient_id'           => 'required|exists:patients,id',
            'funding_cycle_id'     => 'nullable|exists:funding_cycles,id',
            'invoice_reference'    => 'nullable|string|max:255',
            'invoice_date'         => 'required|date',
            'sessions_completed'   => 'nullable|integer|min:0',
            'travel_miles'         => 'nullable|numeric|min:0',
            'session_amount'       => 'nullable|numeric|min:0',
            'travel_amount'        => 'nullable|numeric|min:0',
            'total_amount'         => 'required|numeric|min:0',
            'status'               => 'nullable|string|max:50',
            'payment_date'         => 'nullable|date',
            'due_date'             => 'nullable|date',
            'notes'                => 'nullable|string',
            'document_path'        => 'nullable|string|max:500',
        ]);

        $associateInvoice->update($data);

        return redirect()->route('associate-invoices.show', $associateInvoice)
            ->with('success', 'Associate invoice updated successfully.');
    }

    public function updateStatus(Request $request, AssociateInvoice $associateInvoice)
    {
        $data = $request->validate([
            'status'       => 'required|string|max:50',
            'payment_date' => 'nullable|date',
        ]);

        $associateInvoice->update($data);

        return redirect()->back()->with('success', 'Invoice status updated.');
    }

    public function destroy(AssociateInvoice $associateInvoice)
    {
        $associateInvoice->delete();

        return redirect()->route('associate-invoices.index')
            ->with('success', 'Associate invoice deleted successfully.');
    }
}
