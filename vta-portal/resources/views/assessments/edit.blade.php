@php $header = 'Edit Assessment'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('assessments.update', $assessment) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Agreed Amount (£)</label>
                        <input type="number" name="fee_agreed_amount" step="0.01" min="0" value="{{ old('fee_agreed_amount', $assessment->fee_agreed_amount) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Agreed Document</label>
                        <input type="file" name="fee_agreed_document" class="mt-1 block w-full text-sm text-gray-500">
                        @if($assessment->fee_agreed_document_path)
                        <p class="mt-1 text-xs text-green-600">Document already uploaded.</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date Client Contacted</label>
                        <input type="date" name="date_client_contacted" value="{{ old('date_client_contacted', $assessment->date_client_contacted?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Assessor</label>
                        <input type="text" name="assessor" value="{{ old('assessor', $assessment->assessor) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue', $assessment->venue) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment Date</label>
                        <input type="date" name="assessment_date" value="{{ old('assessment_date', $assessment->assessment_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment Cost (£)</label>
                        <input type="number" name="assessment_cost" step="0.01" min="0" value="{{ old('assessment_cost', $assessment->assessment_cost) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment Cost Document</label>
                        <input type="file" name="assessment_cost_document" class="mt-1 block w-full text-sm text-gray-500">
                        @if($assessment->assessment_cost_document_path)
                        <p class="mt-1 text-xs text-green-600">Document already uploaded.</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Report Sent</label>
                        <select name="report_sent" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="0" @selected(!$assessment->report_sent)>No</option>
                            <option value="1" @selected($assessment->report_sent)>Yes</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Report Document</label>
                        <input type="file" name="report_document" class="mt-1 block w-full text-sm text-gray-500">
                        @if($assessment->report_document_path)
                        <p class="mt-1 text-xs text-green-600">Document already uploaded.</p>
                        @endif
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Special Instructions</label>
                        <textarea name="special_instructions" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('special_instructions', $assessment->special_instructions) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes', $assessment->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('patients.show', $assessment->patient) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Save Assessment</button>
            </div>
        </form>
    </div>
</x-app-layout>
