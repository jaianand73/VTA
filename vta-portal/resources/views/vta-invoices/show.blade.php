@php $header = 'Invoice ' . $vtaInvoice->invoice_number; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        @if($exceedsBalance)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-medium text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> This invoice exceeds the remaining balance of the linked funding cycle.</p>
        </div>
        @endif

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $vtaInvoice->recipient_name }}</p>
            <div class="flex gap-3">
                <a href="{{ route('vta-invoices.edit', $vtaInvoice) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                <a href="{{ route('vta-invoices.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="text-center mb-6 pb-6 border-b border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">&pound;{{ number_format($vtaInvoice->total_amount, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $vtaInvoice->sessions_invoiced ?? '0' }} sessions invoiced</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Invoice Number</p><p class="text-sm font-bold text-gray-900">{{ $vtaInvoice->invoice_number }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Patient</p><p class="text-sm font-medium"><a href="{{ route('patients.show', $vtaInvoice->patient) }}" class="text-[#0092b4] hover:underline">{{ $vtaInvoice->patient?->first_name }} {{ $vtaInvoice->patient?->last_name }}</a></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Invoice Date</p><p class="text-sm font-medium text-gray-900">{{ $vtaInvoice->invoice_date?->format('d/m/Y') }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Due Date</p><p class="text-sm font-medium text-{{ $vtaInvoice->status == 'Overdue' ? 'red' : 'gray' }}-600">{{ $vtaInvoice->due_date?->format('d/m/Y') ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Recipient</p><p class="text-sm font-medium text-gray-900">{{ $vtaInvoice->recipient_name }} ({{ $vtaInvoice->recipient_type }})</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Recipient Email</p><p class="text-sm font-medium text-gray-900">{{ $vtaInvoice->recipient_email ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Session Amount</p><p class="text-sm font-medium text-gray-900">&pound;{{ number_format($vtaInvoice->session_amount ?? 0, 2) }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Additional Charges</p><p class="text-sm font-medium text-gray-900">&pound;{{ number_format($vtaInvoice->additional_charges ?? 0, 2) }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</p>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @switch($vtaInvoice->status)
                            @case('Draft') bg-gray-100 text-gray-800 @break
                            @case('Sent') bg-blue-100 text-blue-800 @break
                            @case('Paid') bg-green-100 text-green-800 @break
                            @case('Overdue') bg-red-100 text-red-800 @break
                            @case('Cancelled') bg-gray-100 text-gray-500 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">{{ $vtaInvoice->status }}</span>
                </div>
                @if($vtaInvoice->payment_date)
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Payment Date</p><p class="text-sm font-medium text-gray-900">{{ $vtaInvoice->payment_date?->format('d/m/Y') }}</p></div>
                @endif
            </div>
            @if($vtaInvoice->recipient_address)
            <div class="mt-6 pt-6 border-t border-gray-100"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Recipient Address</p><p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $vtaInvoice->recipient_address }}</p></div>
            @endif
            @if($vtaInvoice->notes)
            <div class="mt-6 pt-6 border-t border-gray-100"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</p><p class="text-sm text-gray-700">{{ $vtaInvoice->notes }}</p></div>
            @endif
        </div>

        <form method="POST" action="{{ route('vta-invoices.status', $vtaInvoice) }}" enctype="multipart/form-data" class="rounded-xl border border-gray-200 bg-white p-6" data-swal="Update VTA invoice status?">
            @csrf @method('PATCH')
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Update Status</h3>
            @if(!$vtaInvoice->document_path)
            <p class="mb-3 text-xs text-amber-600">No document attached yet — required before this invoice can be marked "Sent" (BR-F6).</p>
            @endif
            <div class="flex flex-wrap gap-3 items-end">
                <div>
                    <select name="status" class="block rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <option value="Draft" {{ $vtaInvoice->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Sent" {{ $vtaInvoice->status == 'Sent' ? 'selected' : '' }}>Sent</option>
                        <option value="Paid" {{ $vtaInvoice->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Overdue" {{ $vtaInvoice->status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="Cancelled" {{ $vtaInvoice->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="payment_date" value="{{ old('payment_date', $vtaInvoice->payment_date?->format('Y-m-d')) }}" placeholder="Payment date" class="block rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Document {{ $vtaInvoice->document_path ? '(replace)' : '(required for Sent)' }}</label>
                    <input type="file" name="document" class="block rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Update</button>
            </div>
        </form>

        <form method="POST" action="{{ route('vta-invoices.destroy', $vtaInvoice) }}" data-swal-label="this VTA invoice">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50"><i class="fa-solid fa-trash-can"></i> Delete</button>
        </form>
    </div>
</x-app-layout>
