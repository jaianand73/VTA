@php $header = 'Edit Associate Invoice'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('associate-invoices.update', $associateInvoice) }}" class="space-y-6">
            @csrf @method('PUT')
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Associate</label><select name="associate_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">@foreach($associates as $a)<option value="{{ $a->id }}" {{ old('associate_id', $associateInvoice->associate_id) == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>@endforeach</select></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</label><select name="patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">@foreach($patients as $p)<option value="{{ $p->id }}" {{ old('patient_id', $associateInvoice->patient_id) == $p->id ? 'selected' : '' }}>{{ $p->first_name }} {{ $p->last_name }}</option>@endforeach</select></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Reference</label><input type="text" name="invoice_reference" value="{{ old('invoice_reference', $associateInvoice->invoice_reference) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Date</label><input type="date" name="invoice_date" value="{{ old('invoice_date', $associateInvoice->invoice_date?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions</label><input type="number" name="sessions_completed" value="{{ old('sessions_completed', $associateInvoice->sessions_completed) }}" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Miles</label><input type="number" name="travel_miles" value="{{ old('travel_miles', $associateInvoice->travel_miles) }}" step="0.1" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Session Amount</label><input type="number" name="session_amount" value="{{ old('session_amount', $associateInvoice->session_amount) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Amount</label><input type="number" name="travel_amount" value="{{ old('travel_amount', $associateInvoice->travel_amount) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</label><input type="number" name="total_amount" value="{{ old('total_amount', $associateInvoice->total_amount) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</label><input type="date" name="due_date" value="{{ old('due_date', $associateInvoice->due_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</label><input type="date" name="payment_date" value="{{ old('payment_date', $associateInvoice->payment_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Received" {{ old('status', $associateInvoice->status) == 'Received' ? 'selected' : '' }}>Received</option>
                            <option value="Verified" {{ old('status', $associateInvoice->status) == 'Verified' ? 'selected' : '' }}>Verified</option>
                            <option value="Paid" {{ old('status', $associateInvoice->status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Disputed" {{ old('status', $associateInvoice->status) == 'Disputed' ? 'selected' : '' }}>Disputed</option>
                        </select>
                    </div>
                </div>
                <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label><textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes', $associateInvoice->notes) }}</textarea></div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('associate-invoices.show', $associateInvoice) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-floppy-disk mr-1"></i> Update</button>
            </div>
        </form>
    </div>
</x-app-layout>
