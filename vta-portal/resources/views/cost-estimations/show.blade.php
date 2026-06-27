@php $header = 'Cost Estimation Details'; @endphp
<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Version {{ $costEstimation->version_number }}</p>
            <div class="flex gap-3">
                <a href="{{ route('cost-estimations.edit', $costEstimation) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                <a href="{{ route('cost-estimations.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Patient</p><p class="text-sm font-medium text-gray-900"><a href="{{ route('patients.show', $costEstimation->patient) }}" class="text-[#0092b4] hover:underline">{{ $costEstimation->patient?->first_name }} {{ $costEstimation->patient?->last_name }}</a></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Title</p><p class="text-sm font-medium text-gray-900">{{ $costEstimation->title ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Estimated Amount</p><p class="text-sm font-bold text-gray-900">&pound;{{ number_format($costEstimation->estimated_amount, 2) }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Estimated Sessions</p><p class="text-sm font-medium text-gray-900">{{ $costEstimation->estimated_sessions ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Estimated Duration</p><p class="text-sm font-medium text-gray-900">{{ $costEstimation->estimated_duration ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sent Date</p><p class="text-sm font-medium text-gray-900">{{ $costEstimation->sent_date?->format('d/m/Y') ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sent To</p><p class="text-sm font-medium text-gray-900">{{ $costEstimation->sent_to ?? '-' }}</p></div>
            </div>
            @if($costEstimation->notes)
            <div class="mt-6 pt-6 border-t border-gray-100"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</p><p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $costEstimation->notes }}</p></div>
            @endif
        </div>
        <form method="POST" action="{{ route('cost-estimations.destroy', $costEstimation) }}" data-swal-label="this cost estimation">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50"><i class="fa-solid fa-trash-can"></i> Delete</button>
        </form>
    </div>
</x-app-layout>
