<?php

namespace App\Http\Controllers;

use App\Models\VtaInvoice;
use App\Models\Patient;
use App\Models\FundingCycle;
use App\Services\InvoiceNumberService;
use App\Services\FundingBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VtaInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = VtaInvoice::with(['patient', 'fundingCycle', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->recipient_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $vtaInvoices = $query->latest()->paginate(20);

        $summary = [
            'invoiced_this_month' => VtaInvoice::whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)->sum('total_amount'),
            'paid_this_month' => VtaInvoice::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)->where('status', 'Paid')->sum('total_amount'),
            'outstanding' => VtaInvoice::whereIn('status', ['Sent', 'Overdue'])->sum('total_amount'),
            'overdue_count' => VtaInvoice::where('status', 'Overdue')->count(),
        ];

        return view('vta-invoices.index', compact('vtaInvoices', 'summary'));
    }

    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        $fundingCycles = FundingCycle::with('patient')->where('is_active', true)->get();
        $balanceService = app(FundingBalanceService::class);

        return view('vta-invoices.create', compact('patients', 'fundingCycles', 'balanceService'));
    }

    public function store(Request $request, InvoiceNumberService $invoiceNumberService, FundingBalanceService $balanceService)
    {
        $data = $request->validate([
            'patient_id'         => 'required|exists:patients,id',
            'funding_cycle_id'   => 'nullable|exists:funding_cycles,id',
            'invoice_date'       => 'required|date',
            'due_date'           => 'nullable|date',
            'recipient_type'     => 'required|string|max:50',
            'recipient_name'     => 'required|string|max:255',
            'recipient_email'    => 'nullable|email|max:255',
            'recipient_address'  => 'nullable|string',
            'sessions_invoiced'  => 'nullable|integer|min:0',
            'session_amount'     => 'nullable|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'total_amount'       => 'required|numeric|min:0',
            'status'             => 'nullable|string|max:50',
            'document_path'      => 'nullable|string|max:500',
            'notes'              => 'nullable|string',
        ]);

        if ($data['funding_cycle_id'] ?? false) {
            $cycle = FundingCycle::find($data['funding_cycle_id']);
            if ($cycle && $balanceService->willExceedBalance($cycle, $data['total_amount'])) {
                session()->flash('warning', 'This invoice exceeds the remaining balance of the funding cycle.');
            }
        }

        $data['invoice_number'] = $invoiceNumberService->generate();
        $data['created_by'] = Auth::id();

        VtaInvoice::create($data);

        return redirect()->route('vta-invoices.index')
            ->with('success', 'VTA invoice created successfully. Number: ' . $data['invoice_number']);
    }

    public function show(VtaInvoice $vtaInvoice)
    {
        $vtaInvoice->load(['patient', 'fundingCycle', 'createdBy']);
        $balanceService = app(FundingBalanceService::class);
        $exceedsBalance = false;

        if ($vtaInvoice->fundingCycle) {
            $exceedsBalance = $balanceService->willExceedBalance(
                $vtaInvoice->fundingCycle, $vtaInvoice->total_amount
            );
        }

        return view('vta-invoices.show', compact('vtaInvoice', 'exceedsBalance'));
    }

    public function edit(VtaInvoice $vtaInvoice)
    {
        $patients = Patient::orderBy('first_name')->get();
        $fundingCycles = FundingCycle::with('patient')->where('is_active', true)->get();
        $balanceService = app(FundingBalanceService::class);

        return view('vta-invoices.edit', compact('vtaInvoice', 'patients', 'fundingCycles', 'balanceService'));
    }

    public function update(Request $request, VtaInvoice $vtaInvoice)
    {
        $data = $request->validate([
            'patient_id'         => 'required|exists:patients,id',
            'funding_cycle_id'   => 'nullable|exists:funding_cycles,id',
            'invoice_date'       => 'required|date',
            'due_date'           => 'nullable|date',
            'recipient_type'     => 'required|string|max:50',
            'recipient_name'     => 'required|string|max:255',
            'recipient_email'    => 'nullable|email|max:255',
            'recipient_address'  => 'nullable|string',
            'sessions_invoiced'  => 'nullable|integer|min:0',
            'session_amount'     => 'nullable|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'total_amount'       => 'required|numeric|min:0',
            'status'             => 'nullable|string|max:50',
            'payment_date'       => 'nullable|date',
            'document_path'      => 'nullable|string|max:500',
            'notes'              => 'nullable|string',
        ]);

        $vtaInvoice->update($data);

        return redirect()->route('vta-invoices.show', $vtaInvoice)
            ->with('success', 'VTA invoice updated successfully.');
    }

    public function updateStatus(Request $request, VtaInvoice $vtaInvoice)
    {
        $data = $request->validate([
            'status'       => 'required|string|max:50',
            'payment_date' => 'nullable|date',
            'document'     => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,jpg,png,gif',
        ]);

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('vta-invoices', 'vta-documents');
            $data['document_path'] = $path;
        }
        unset($data['document']);

        if ($data['status'] === 'Sent' && !($data['document_path'] ?? $vtaInvoice->document_path)) {
            return back()->withErrors(['status' => 'Cannot mark invoice as Sent without an uploaded document.']);
        }

        $vtaInvoice->update($data);

        return redirect()->back()->with('success', 'Invoice status updated.');
    }

    public function destroy(VtaInvoice $vtaInvoice)
    {
        $vtaInvoice->delete();

        return redirect()->route('vta-invoices.index')
            ->with('success', 'VTA invoice deleted successfully.');
    }
}
