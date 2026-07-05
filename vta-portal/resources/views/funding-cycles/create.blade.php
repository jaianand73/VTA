@php $header = 'Create Funding Cycle'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('funding-cycles.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4" x-data="{
                selectedPatient: {{ old('patient_id', $preselectedPatient?->id ?? 'null') }},
                get filteredEstimations() {
                    return estimationsByPatient[this.selectedPatient] || [];
                }
            }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" x-model="selectedPatient" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Patient</option>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}" @if(old('patient_id', $preselectedPatient?->id) == $p->id) selected @endif>{{ $p->first_name }} {{ $p->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Estimation</label>
                        <select name="cost_estimation_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">None</option>
                            <template x-for="ce in filteredEstimations" :key="ce.id">
                                <option :value="ce.id" x-text="ce.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Amount <span class="text-red-500">*</span></label>
                        <input type="number" name="approved_amount" value="{{ old('approved_amount') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Sessions</label>
                        <input type="number" name="approved_sessions" value="{{ old('approved_sessions') }}" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Date <span class="text-red-500">*</span></label>
                        <input type="date" name="approval_date" value="{{ old('approval_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Estimated Duration</label>
                        <input type="text" name="estimated_duration" value="{{ old('estimated_duration') }}" placeholder="e.g. 6 months" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Funder Name</label>
                        <input type="text" name="funder_name" value="{{ old('funder_name') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Funder Reference</label>
                        <input type="text" name="funder_reference" value="{{ old('funder_reference') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Document</label>
                        <input type="file" name="approval_document" accept=".pdf,.doc,.docx,.jpg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-[#0092b4]/10 file:px-4 file:py-2 file:text-sm file:font-medium file:text-[#0092b4] hover:file:bg-[#0092b4]/20">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label>
                    <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('funding-cycles.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-floppy-disk mr-1"></i> Save Funding Cycle</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const estimationsByPatient = @json($estimationsByPatient);
    </script>
    @endpush
</x-app-layout>
