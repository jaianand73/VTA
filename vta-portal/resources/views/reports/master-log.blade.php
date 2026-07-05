<x-app-layout>
    <x-slot name="header">Master Log</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500">Active patients with funding and expense summary. Excludes Not Proceeding and Case Closed.</p>
        <a href="{{ route('reports.index') }}" class="text-sm text-[#0092b4] hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Reports</a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Approved (£)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">VTA Paid (£)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Assoc Paid (£)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Balance (£)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $row)
                @php
                    $p = $row['patient'];
                    $isLow = $row['approved'] > 0 && $row['balance'] < ($row['approved'] * 0.2);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('patients.show', $p) }}" class="font-medium text-[#0092b4] hover:underline">
                            {{ $p->first_name }} {{ $p->last_name }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $p->status }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->caseManager?->company?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">{{ $row['approved'] ? number_format($row['approved'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-green-700">{{ number_format($row['vta_paid'], 2) }}</td>
                    <td class="px-4 py-3 text-right text-red-600">{{ number_format($row['assoc_paid'], 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $isLow ? 'text-red-600' : 'text-gray-800' }}">
                        {{ number_format($row['balance'], 2) }}
                        @if($isLow)<i class="fa-solid fa-triangle-exclamation text-xs ml-1"></i>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No active patients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
