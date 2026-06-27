<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-800">Finance Reports</h1>
            <a href="{{ route('finance.index') }}" class="text-sm text-[#0092b4] hover:text-[#007a9a]">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Finance
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Revenue Summary</h3>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-sm text-gray-500">Total Invoiced (Month)</p>
                    <p class="text-xl font-semibold text-gray-800">£{{ number_format($revenueSummary['total_invoiced_month'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Invoiced (Year)</p>
                    <p class="text-xl font-semibold text-gray-800">£{{ number_format($revenueSummary['total_invoiced_year'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Paid (Year)</p>
                    <p class="text-xl font-semibold text-gray-800">£{{ number_format($revenueSummary['total_paid_year'], 2) }}</p>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-sm font-medium text-gray-700 mb-2">Outstanding by Age</p>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-amber-50 p-3">
                        <p class="text-sm text-amber-700">0-30 Days</p>
                        <p class="text-lg font-semibold text-amber-800">£{{ number_format($revenueSummary['outstanding_0_30'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-orange-50 p-3">
                        <p class="text-sm text-orange-700">31-60 Days</p>
                        <p class="text-lg font-semibold text-orange-800">£{{ number_format($revenueSummary['outstanding_31_60'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-3">
                        <p class="text-sm text-red-700">60+ Days</p>
                        <p class="text-lg font-semibold text-red-800">£{{ number_format($revenueSummary['outstanding_61_plus'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Revenue by Company</h3>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Invoiced</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Paid</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($revenueByCompany as $rc)
                        <tr class="even:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $rc['name'] }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-600">£{{ number_format($rc['total_invoiced'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-600">£{{ number_format($rc['total_paid'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-medium {{ $rc['outstanding'] > 0 ? 'text-red-600' : 'text-gray-600' }}">
                                £{{ number_format($rc['outstanding'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No data yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Associate Payments</h3>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Associate</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Invoiced</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Paid</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Overdue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($associatePayments as $ap)
                        <tr class="even:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $ap->associate->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-600">£{{ number_format($ap->total_invoiced, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-600">£{{ number_format($ap->total_paid, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-medium {{ $ap->overdue > 0 ? 'text-red-600' : 'text-gray-600' }}">
                                £{{ number_format($ap->overdue, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No data yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
