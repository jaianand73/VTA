@php $header = 'Edit Funding Cycle'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('funding-cycles.update', $fundingCycle) }}" class="space-y-6">
            @csrf @method('PUT')
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</label><select name="patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">@foreach($patients as $p)<option value="{{ $p->id }}" {{ old('patient_id', $fundingCycle->patient_id) == $p->id ? 'selected' : '' }}>{{ $p->first_name }} {{ $p->last_name }}</option>@endforeach</select></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Estimation</label><select name="cost_estimation_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"><option value="">None</option>@foreach($costEstimations as $ce)<option value="{{ $ce->id }}" {{ old('cost_estimation_id', $fundingCycle->cost_estimation_id) == $ce->id ? 'selected' : '' }}>{{ $ce->patient?->first_name }} {{ $ce->patient?->last_name }} - v{{ $ce->version_number }}</option>@endforeach</select></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Amount</label><input type="number" name="approved_amount" value="{{ old('approved_amount', $fundingCycle->approved_amount) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Sessions</label><input type="number" name="approved_sessions" value="{{ old('approved_sessions', $fundingCycle->approved_sessions) }}" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Date</label><input type="date" name="approval_date" value="{{ old('approval_date', $fundingCycle->approval_date?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</label><input type="text" name="estimated_duration" value="{{ old('estimated_duration', $fundingCycle->estimated_duration) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Funder Name</label><input type="text" name="funder_name" value="{{ old('funder_name', $fundingCycle->funder_name) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                    <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Funder Reference</label><input type="text" name="funder_reference" value="{{ old('funder_reference', $fundingCycle->funder_reference) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $fundingCycle->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>
                <div><label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label><textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes', $fundingCycle->notes) }}</textarea></div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('funding-cycles.show', $fundingCycle) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-floppy-disk mr-1"></i> Update</button>
            </div>
        </form>
    </div>
</x-app-layout>
