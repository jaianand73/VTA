@php $header = 'Associate Activity'; @endphp
<x-app-layout>
    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="text-sm text-[#0092b4] hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Reports</a>
    </div>
    <form method="GET" class="mb-4 flex gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500">From</label>
            <input type="date" name="from" value="{{ $from }}" class="mt-1 block rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">To</label>
            <input type="date" name="to" value="{{ $to }}" class="mt-1 block rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Filter</button>
    </form>
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <th class="px-4 py-3">Associate</th>
                    <th class="px-4 py-3 text-center">Patient Notes</th>
                    <th class="px-4 py-3 text-center">Signed Off</th>
                    <th class="px-4 py-3 text-center">Notes Pending</th>
                    <th class="px-4 py-3 text-center" style="border-left:2px solid #d1fae5;">
                        <span style="color:#059669;">Referral Sessions</span>
                    </th>
                    <th class="px-4 py-3 text-right" style="color:#059669;">Bills (£)</th>
                    <th class="px-4 py-3 text-center text-gray-500">Audit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($allAssociateIds as $assocId)
                @php
                    $assoc   = $associates[$assocId] ?? null;
                    $note    = $notes[$assocId] ?? null;
                    $refSess = $refSessionCounts[$assocId] ?? 0;
                    $refBill = $refBillTotals[$assocId] ?? 0;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $assoc?->name ?? 'Unknown' }}</td>
                    <td class="px-4 py-3 text-center text-gray-700">{{ $note?->total_notes ?? 0 }}</td>
                    <td class="px-4 py-3 text-center text-green-600">{{ $note?->signed_off ?? 0 }}</td>
                    <td class="px-4 py-3 text-center {{ ($note?->total_notes - $note?->signed_off) > 0 ? 'text-amber-600 font-medium' : 'text-gray-400' }}">
                        {{ ($note?->total_notes ?? 0) - ($note?->signed_off ?? 0) }}
                    </td>
                    <td class="px-4 py-3 text-center font-medium" style="border-left:2px solid #d1fae5;color:{{ $refSess > 0 ? '#059669' : '#9ca3af' }};">
                        {{ $refSess }}
                    </td>
                    <td class="px-4 py-3 text-right {{ $refBill > 0 ? 'font-medium' : 'text-gray-400' }}" style="color:{{ $refBill > 0 ? '#059669' : '#9ca3af' }};">
                        {{ $refBill > 0 ? '£'.number_format($refBill, 2) : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($assoc)
                        <a href="{{ route('audit.associate', ['associate_id' => $assoc->id, 'all_time' => 1]) }}"
                           class="text-xs hover:underline" style="color:#7c3aed;">
                            <i class="fa-solid fa-magnifying-glass-chart mr-1"></i>Full Audit
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No activity found in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-xs text-gray-400">
        <span style="color:#059669;">Green columns</span> = referral/assessment stage activity.
        Patient notes = patient-stage only.
        Date range applies to session dates and bill dates respectively.
    </p>
</x-app-layout>
