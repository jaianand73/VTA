@php $header = 'Log Associate Invoice'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('associate-invoices.store') }}" class="space-y-6">
            @csrf
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Associate <span class="text-red-500">*</span></label>
                        <select name="associate_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Associate</option>
                            @foreach($associates as $a)
                            <option value="{{ $a->id }}" {{ old('associate_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Patient</option>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->first_name }} {{ $p->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Funding Cycle</label>
                        <select name="funding_cycle_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">None</option>
                            @foreach($fundingCycles as $fc)
                            <option value="{{ $fc->id }}" {{ old('funding_cycle_id') == $fc->id ? 'selected' : '' }}>Cycle {{ $fc->cycle_number }} - {{ $fc->patient?->first_name }} {{ $fc->patient?->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Reference</label><input type="text" name="invoice_reference" value="{{ old('invoice_reference') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Date <span class="text-red-500">*</span></label><input type="date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions Completed</label><input type="number" name="sessions_completed" value="{{ old('sessions_completed') }}" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Session Amount</label><input type="number" name="session_amount" value="{{ old('session_amount') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Miles</label><input type="number" name="travel_miles" value="{{ old('travel_miles') }}" step="0.1" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Amount</label><input type="number" name="travel_amount" value="{{ old('travel_amount') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount <span class="text-red-500">*</span></label><input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                </div>
                <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label><textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea></div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('associate-invoices.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-floppy-disk mr-1"></i> Save Invoice</button>
            </div>
        </form>
    </div>
</x-app-layout>
