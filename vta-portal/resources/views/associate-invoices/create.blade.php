@php $header = 'Log Associate Invoice'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('associate-invoices.store') }}" class="space-y-6">
            @csrf
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4" x-data="{
                selectedAssociate: {{ old('associate_id', 'null') }},
                sessions: 0,
                mileage: 0,
                rate: 0,
                mileRate: 0,
                get patients() {
                    return patientsByAssociate[this.selectedAssociate] || [];
                },
                get calculatedTotal() {
                    return (this.rate * this.sessions + this.mileRate * this.mileage).toFixed(2);
                },
                setRate() {
                    const sel = document.querySelector('select[name=\\'associate_id\\']');
                    if (sel) {
                        const opt = sel.options[sel.selectedIndex];
                        this.rate = parseFloat(opt?.dataset?.rate || 0);
                        this.mileRate = parseFloat(opt?.dataset?.mileRate || 0);
                    }
                }
            }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Associate <span class="text-red-500">*</span></label>
                        <select name="associate_id" x-model="selectedAssociate" @change="setRate()" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Associate</option>
                            @foreach($associates as $a)
                            <option value="{{ $a->id }}" data-rate="{{ $a->session_rate }}" data-mile-rate="{{ $a->travel_rate_per_mile }}" {{ old('associate_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Patient</option>
                            <template x-for="p in patients" :key="p.id">
                                <option :value="p.id" x-text="p.name"></option>
                            </template>
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
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions Completed</label><input type="number" name="sessions_completed" x-model="sessions" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Session Amount</label><input type="number" name="session_amount" :value="(rate * sessions).toFixed(2)" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Miles</label><input type="number" name="travel_miles" x-model="mileage" step="0.1" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Amount</label><input type="number" name="travel_amount" :value="(mileRate * mileage).toFixed(2)" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="total_amount" :value="calculatedTotal" step="0.01" min="0" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4">
                            <p class="mt-1 text-xs text-gray-400">Auto-calculated from rate card. Manually override if needed.</p>
                        </div>
                    </div>
                </div>
                <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label><textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea></div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('accounts.index', ['tab' => 'associate']) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-floppy-disk mr-1"></i> Save Invoice</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const patientsByAssociate = @json($patientsByAssociate);
    </script>
    @endpush
</x-app-layout>
