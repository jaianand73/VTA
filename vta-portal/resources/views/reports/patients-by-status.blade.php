@php $header = 'Patients by Status'; @endphp
<x-app-layout>
    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="text-sm text-[#0092b4] hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Reports</a>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Count</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($patients as $row)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $row->status }}</td>
                    <td class="px-4 py-3">{{ $row->count }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-8 text-center text-gray-500">No patients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
