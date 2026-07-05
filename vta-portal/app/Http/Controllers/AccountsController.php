<?php

namespace App\Http\Controllers;

use App\Models\VtaInvoice;
use App\Models\AssociateInvoice;
use App\Models\Associate;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'vta');

        // VTA Invoices
        $vtaQuery = VtaInvoice::with(['patient', 'fundingCycle', 'createdBy']);
        if ($request->filled('vta_status'))    { $vtaQuery->where('status', $request->vta_status); }
        if ($request->filled('vta_date_from')) { $vtaQuery->whereDate('invoice_date', '>=', $request->vta_date_from); }
        if ($request->filled('vta_date_to'))   { $vtaQuery->whereDate('invoice_date', '<=', $request->vta_date_to); }
        $vtaInvoices = $vtaQuery->latest()->paginate(20, ['*'], 'vta_page');

        $vtaSummary = [
            'invoiced_this_month' => VtaInvoice::whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('total_amount'),
            'paid_this_month'     => VtaInvoice::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->where('status', 'Paid')->sum('total_amount'),
            'outstanding'         => VtaInvoice::whereIn('status', ['Sent', 'Overdue'])->sum('total_amount'),
            'overdue_count'       => VtaInvoice::where('status', 'Overdue')->count(),
        ];

        // Associate Invoices
        $assocQuery = AssociateInvoice::with(['associate', 'patient', 'fundingCycle', 'loggedBy']);
        if ($request->filled('assoc_associate_id')) { $assocQuery->where('associate_id', $request->assoc_associate_id); }
        if ($request->filled('assoc_status'))       { $assocQuery->where('status', $request->assoc_status); }
        if ($request->filled('assoc_date_from'))    { $assocQuery->whereDate('invoice_date', '>=', $request->assoc_date_from); }
        if ($request->filled('assoc_date_to'))      { $assocQuery->whereDate('invoice_date', '<=', $request->assoc_date_to); }
        $associateInvoices = $assocQuery->latest()->paginate(20, ['*'], 'assoc_page');

        $assocSummary = [
            'received_this_month' => AssociateInvoice::whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('total_amount'),
            'paid_this_month'     => AssociateInvoice::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->where('status', 'Paid')->sum('total_amount'),
            'overdue_count'       => AssociateInvoice::whereIn('status', ['Received', 'Verified'])->where('due_date', '<', now())->count(),
        ];

        $associates = Associate::where('is_active', true)->orderBy('name')->get();

        return view('accounts.index', compact(
            'tab', 'vtaInvoices', 'vtaSummary', 'associateInvoices', 'assocSummary', 'associates'
        ));
    }
}
