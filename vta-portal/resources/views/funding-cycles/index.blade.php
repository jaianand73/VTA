@php $header = 'Funding Cycles'; @endphp
<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">All funding cycles</p>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('funding-cycles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-plus"></i> Add Funding Cycle</a>
            @endif
        </div>
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cycle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Approved Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Approval Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($fundingCycles as $fc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $fc->patient?->first_name }} {{ $fc->patient?->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">Cycle {{ $fc->cycle_number }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">&pound;{{ number_format($fc->approved_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $fc->approved_sessions ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $fc->approval_date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if($fc->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm"><a href="{{ route('funding-cycles.show', $fc) }}" class="text-[#0092b4] hover:text-[#007a9a]"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500"><p>No funding cycles found.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $fundingCycles->links() }}</div>
    </div>
</x-app-layout>
