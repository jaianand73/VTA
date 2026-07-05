@php $header = 'Associate Invoice'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $associateInvoice->invoice_reference ?? 'No Reference' }}</p>
            <div class="flex gap-3">
                <a href="{{ route('associate-invoices.edit', $associateInvoice) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                <a href="{{ route('accounts.index', ['tab' => 'associate']) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Associate</p><p class="text-sm font-medium text-gray-900">{{ $associateInvoice->associate?->name ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Patient</p><p class="text-sm font-medium text-gray-900"><a href="{{ route('patients.show', $associateInvoice->patient) }}" class="text-[#0092b4] hover:underline">{{ $associateInvoice->patient?->first_name }} {{ $associateInvoice->patient?->last_name }}</a></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Invoice Date</p><p class="text-sm font-medium text-gray-900">{{ $associateInvoice->invoice_date?->format('d/m/Y') }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Due Date</p><p class="text-sm font-medium text-{{ $associateInvoice->due_date && $associateInvoice->due_date < now() ? 'red' : 'gray' }}-600">{{ $associateInvoice->due_date?->format('d/m/Y') ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sessions</p><p class="text-sm font-medium text-gray-900">{{ $associateInvoice->sessions_completed ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Travel Miles</p><p class="text-sm font-medium text-gray-900">{{ $associateInvoice->travel_miles ?? '0' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Session Amount</p><p class="text-sm font-medium text-gray-900">&pound;{{ number_format($associateInvoice->session_amount ?? 0, 2) }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Travel Amount</p><p class="text-sm font-medium text-gray-900">&pound;{{ number_format($associateInvoice->travel_amount ?? 0, 2) }}</p></div>
                <div class="md:col-span-2"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Amount</p><p class="text-xl font-bold text-gray-900">&pound;{{ number_format($associateInvoice->total_amount, 2) }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</p>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @switch($associateInvoice->status)
                            @case('Received') bg-blue-100 text-blue-800 @break
                            @case('Verified') bg-green-100 text-green-800 @break
                            @case('Paid') bg-emerald-100 text-emerald-800 @break
                            @case('Disputed') bg-red-100 text-red-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">{{ $associateInvoice->status }}</span>
                </div>
                @if($associateInvoice->payment_date)
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Payment Date</p><p class="text-sm font-medium text-gray-900">{{ $associateInvoice->payment_date?->format('d/m/Y') }}</p></div>
                @endif
            </div>
            @if($associateInvoice->notes)
            <div class="mt-6 pt-6 border-t border-gray-100"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</p><p class="text-sm text-gray-700">{{ $associateInvoice->notes }}</p></div>
            @endif
        </div>

        <form method="POST" action="{{ route('associate-invoices.status', $associateInvoice) }}" class="rounded-xl border border-gray-200 bg-white p-6" data-swal="Update associate invoice status?">
            @csrf @method('PATCH')
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Update Status</h3>
            <div class="flex gap-3 items-end">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</label>
                    <select name="status" class="mt-1 block rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <option value="Received" {{ $associateInvoice->status == 'Received' ? 'selected' : '' }}>Received</option>
                        <option value="Verified" {{ $associateInvoice->status == 'Verified' ? 'selected' : '' }}>Verified</option>
                        <option value="Paid" {{ $associateInvoice->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Disputed" {{ $associateInvoice->status == 'Disputed' ? 'selected' : '' }}>Disputed</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', $associateInvoice->payment_date?->format('Y-m-d')) }}" class="mt-1 block rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Update</button>
            </div>
        </form>

        <form method="POST" action="{{ route('associate-invoices.destroy', $associateInvoice) }}" data-swal-label="this associate invoice">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50"><i class="fa-solid fa-trash-can"></i> Delete</button>
        </form>
    </div>
</x-app-layout>
