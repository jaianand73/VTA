@php $header = 'Funding Balance Report'; @endphp
<x-app-layout>
    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="text-sm text-[#0092b4] hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Reports</a>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <th class="px-4 py-3">Patient</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Active Cycle</th>
                    <th class="px-4 py-3">Approved</th>
                    <th class="px-4 py-3">Used</th>
                    <th class="px-4 py-3">Remaining</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($activePatients as $patient)
                @php $activeCycle = $patient->fundingCycles->firstWhere('is_active', true); @endphp
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700">{{ $patient->status }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $activeCycle ? 'Cycle ' . $activeCycle->cycle_number : '—' }}</td>
                    <td class="px-4 py-3">£{{ number_format($activeCycle?->approved_amount ?? 0, 2) }}</td>
                    <td class="px-4 py-3">£0.00</td>
                    <td class="px-4 py-3 font-medium">£{{ number_format($activeCycle?->approved_amount ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No patients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
