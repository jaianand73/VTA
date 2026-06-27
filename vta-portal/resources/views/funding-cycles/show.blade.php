@php $header = 'Funding Cycle #' . $fundingCycle->cycle_number; @endphp
<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $fundingCycle->patient?->first_name }} {{ $fundingCycle->patient?->last_name }}</p>
            <div class="flex gap-3">
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('funding-cycles.edit', $fundingCycle) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                @endif
                <a href="{{ route('funding-cycles.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">&pound;{{ number_format($fundingCycle->approved_amount, 2) }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</p>
                    <p class="mt-2 text-2xl font-bold text-{{ $remaining > 0 ? 'emerald' : 'red' }}-600">&pound;{{ number_format($remaining, 2) }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</p>
                    <p class="mt-2 text-2xl font-bold text-[#0092b4]">{{ $usagePercent }}%</p>
                </div>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                <div class="bg-[#0092b4] h-2.5 rounded-full" style="width: {{ $usagePercent }}%"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Patient</p><p class="text-sm font-medium"><a href="{{ route('patients.show', $fundingCycle->patient) }}" class="text-[#0092b4] hover:underline">{{ $fundingCycle->patient?->first_name }} {{ $fundingCycle->patient?->last_name }}</a></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Cycle Number</p><p class="text-sm font-medium text-gray-900">{{ $fundingCycle->cycle_number }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Approved Sessions</p><p class="text-sm font-medium text-gray-900">{{ $fundingCycle->approved_sessions ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Approval Date</p><p class="text-sm font-medium text-gray-900">{{ $fundingCycle->approval_date?->format('d/m/Y') }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Duration</p><p class="text-sm font-medium text-gray-900">{{ $fundingCycle->estimated_duration ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Funder</p><p class="text-sm font-medium text-gray-900">{{ $fundingCycle->funder_name ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Reference</p><p class="text-sm font-medium text-gray-900">{{ $fundingCycle->funder_reference ?? '-' }}</p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</p>@if($fundingCycle->is_active)<span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>@else<span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactive</span>@endif</div>
            </div>
        </div>

        @if($fundingCycle->vtaInvoices->count())
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">VTA Invoices (This Cycle)</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice #</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($fundingCycle->vtaInvoices as $inv)
                    <tr>
                        <td class="px-4 py-2 text-sm text-[#0092b4]"><a href="{{ route('vta-invoices.show', $inv) }}" class="hover:underline">{{ $inv->invoice_number }}</a></td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $inv->invoice_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm font-medium">&pound;{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="px-4 py-2"><span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-{{ $inv->status == 'Paid' ? 'green' : ($inv->status == 'Overdue' ? 'red' : 'blue') }}-100 text-{{ $inv->status == 'Paid' ? 'green' : ($inv->status == 'Overdue' ? 'red' : 'blue') }}-800">{{ $inv->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($fundingCycle->notes)
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</p>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $fundingCycle->notes }}</p>
        </div>
        @endif

        @if(Auth::user()->role === 'admin')
        <form method="POST" action="{{ route('funding-cycles.destroy', $fundingCycle) }}" data-swal-label="this funding cycle">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50"><i class="fa-solid fa-trash-can"></i> Delete</button>
        </form>
        @endif
    </div>
</x-app-layout>
