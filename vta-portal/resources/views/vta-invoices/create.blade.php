@php $header = 'Create VTA Invoice'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto">
        @if(session('warning'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 mb-6">
            <p class="text-sm font-medium text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('warning') }}</p>
        </div>
        @endif

        @php $patientJson = json_encode($patientData); @endphp
        <form method="POST" action="{{ route('vta-invoices.store') }}" class="space-y-6">
            @csrf
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-xs text-gray-400">Invoice number is auto-generated on save (format: VTA-YYYY-NNNN).</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" id="vta_patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Patient</option>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->first_name }} {{ $p->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Funding Cycle</label>
                        <select name="funding_cycle_id" id="vta_funding_cycle_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">None</option>
                            @foreach($fundingCycles as $fc)
                            <option value="{{ $fc->id }}" {{ old('funding_cycle_id') == $fc->id ? 'selected' : '' }}
                                data-amount="{{ $fc->approved_amount }}"
                                data-remaining="{{ $balanceService->remainingBalance($fc) }}">
                                Cycle {{ $fc->cycle_number }} - {{ $fc->patient?->first_name }} {{ $fc->patient?->last_name }}
                                (&pound;{{ number_format($fc->approved_amount, 0) }} approved)
                            </option>
                            @endforeach
                        </select>
                        <div id="cycle-balance-display" class="mt-1 text-xs text-gray-500 hidden"></div>
                    </div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Date</label><input type="date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</label><input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient Type <span class="text-red-500">*</span></label>
                        <select name="recipient_type" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Case Manager Company" {{ old('recipient_type') == 'Case Manager Company' ? 'selected' : '' }}>Case Manager Company</option>
                            <option value="Solicitor" {{ old('recipient_type') == 'Solicitor' ? 'selected' : '' }}>Solicitor</option>
                            <option value="Insurance Company" {{ old('recipient_type') == 'Insurance Company' ? 'selected' : '' }}>Insurance Company</option>
                            <option value="Other" {{ old('recipient_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name <span class="text-red-500">*</span></label><input type="text" name="recipient_name" id="vta_recipient_name" value="{{ old('recipient_name') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Case Manager</label><input type="text" id="vta_case_manager_display" value="{{ old('case_manager_display') }}" placeholder="Auto-filled from patient" readonly class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient Email(s)</label>
                        <input type="text" name="recipient_email" id="vta_recipient_email" value="{{ old('recipient_email') }}" placeholder="email@example.com, another@example.com" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <p class="mt-1 text-xs text-gray-400">Separate multiple addresses with a comma.</p>
                    </div>
                    <div class="md:col-span-2"><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient Address</label><textarea name="recipient_address" id="vta_recipient_address" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('recipient_address') }}</textarea></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions Invoiced</label><input type="number" name="sessions_invoiced" value="{{ old('sessions_invoiced') }}" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Session Amount</label><input type="number" name="session_amount" value="{{ old('session_amount') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Additional Charges</label><input type="number" name="additional_charges" value="{{ old('additional_charges') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount <span class="text-red-500">*</span></label><input type="number" name="total_amount" id="vta_total_amount" value="{{ old('total_amount', $assessment?->assessment_cost ?? '') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
@if($assessment)
<input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
@endif
                </div>
                <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label><textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea></div>
            </div>
            <div id="exceed-warning" class="hidden rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> <span id="exceed-message"></span></p>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('accounts.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-floppy-disk mr-1"></i> Create Invoice</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const vtaPatientData = @json($patientData);

        document.getElementById('vta_patient_id').addEventListener('change', function() {
            const data = vtaPatientData[this.value] || {};
            document.getElementById('vta_recipient_name').value = data.company_name || '';
            document.getElementById('vta_case_manager_display').value = data.case_manager_name || '';
            document.getElementById('vta_recipient_email').value = data.case_manager_email || data.company_email || '';
            document.getElementById('vta_recipient_address').value = data.company_address || '';
        });

        document.getElementById('vta_funding_cycle_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const remaining = selected.dataset.remaining;
            const display = document.getElementById('cycle-balance-display');
            if (remaining !== undefined) {
                display.textContent = 'Remaining balance: £' + parseFloat(remaining).toFixed(2);
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        });

        document.getElementById('vta_total_amount').addEventListener('input', function() {
            const fcSelect = document.getElementById('vta_funding_cycle_id');
            const selected = fcSelect.options[fcSelect.selectedIndex];
            const remaining = parseFloat(selected.dataset.remaining || 0);
            const amount = parseFloat(this.value || 0);
            const warning = document.getElementById('exceed-warning');
            const msg = document.getElementById('exceed-message');

            if (amount > remaining && remaining > 0) {
                warning.classList.remove('hidden');
                msg.textContent = 'This invoice (£' + amount.toFixed(2) + ') exceeds the remaining balance (£' + remaining.toFixed(2) + ') of the selected funding cycle.';
            } else {
                warning.classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
