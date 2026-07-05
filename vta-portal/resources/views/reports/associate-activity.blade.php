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
                    <th class="px-4 py-3">Total Notes</th>
                    <th class="px-4 py-3">Signed Off</th>
                    <th class="px-4 py-3">Pending</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($notes as $note)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $note->associate?->name ?? 'Unknown' }}</td>
                    <td class="px-4 py-3">{{ $note->total_notes }}</td>
                    <td class="px-4 py-3">{{ $note->signed_off }}</td>
                    <td class="px-4 py-3">{{ $note->total_notes - $note->signed_off }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No activity found in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
