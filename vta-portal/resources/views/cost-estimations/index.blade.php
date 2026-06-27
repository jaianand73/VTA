@php $header = 'Cost Estimations'; @endphp
<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">All cost estimations</p>
            <a href="{{ route('cost-estimations.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-plus"></i> Add Cost Estimation</a>
        </div>
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Version</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sent Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($costEstimations as $ce)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $ce->patient?->first_name }} {{ $ce->patient?->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">v{{ $ce->version_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ce->title ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">&pound;{{ number_format($ce->estimated_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ce->estimated_sessions ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ce->sent_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('cost-estimations.show', $ce) }}" class="text-[#0092b4] hover:text-[#007a9a]"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500"><p>No cost estimations found.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $costEstimations->links() }}</div>
    </div>
</x-app-layout>
