@php $header = 'Associate Invoices'; @endphp
<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Invoices received from associates</p>
            <a href="{{ route('associate-invoices.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]"><i class="fa-solid fa-plus"></i> Log Invoice</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Received This Month</p><p class="mt-1 text-xl font-bold text-gray-900">&pound;{{ number_format($summary['received_this_month'], 2) }}</p></div>
            <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Paid This Month</p><p class="mt-1 text-xl font-bold text-green-600">&pound;{{ number_format($summary['paid_this_month'], 2) }}</p></div>
            <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue</p><p class="mt-1 text-xl font-bold text-{{ $summary['overdue_count'] > 0 ? 'red' : 'gray' }}-600">{{ $summary['overdue_count'] }}</p></div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Associate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($associateInvoices as $inv)
                    <tr class="hover:bg-gray-50 {{ $inv->due_date && $inv->due_date < now() && in_array($inv->status, ['Received','Verified']) ? 'bg-amber-50' : '' }}">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $inv->associate?->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->patient?->first_name }} {{ $inv->patient?->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->invoice_reference ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->invoice_date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">&pound;{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm {{ $inv->due_date && $inv->due_date < now() ? 'text-red-600 font-medium' : 'text-gray-600' }}">{{ $inv->due_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @switch($inv->status)
                                    @case('Received') bg-blue-100 text-blue-800 @break
                                    @case('Verified') bg-green-100 text-green-800 @break
                                    @case('Paid') bg-emerald-100 text-emerald-800 @break
                                    @case('Disputed') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch">{{ $inv->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm"><a href="{{ route('associate-invoices.show', $inv) }}" class="text-[#0092b4] hover:text-[#007a9a]"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500"><p>No associate invoices found.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $associateInvoices->links() }}</div>
    </div>
</x-app-layout>
