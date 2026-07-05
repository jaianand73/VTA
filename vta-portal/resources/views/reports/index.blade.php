@php $header = 'Reports'; @endphp
<x-app-layout>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('reports.funding-balance') }}" class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-800">Funding Balance</h3>
            <p class="text-sm text-gray-500 mt-2">Funding/session balance per active patient</p>
        </a>
        <a href="{{ route('reports.financial-summary') }}" class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-800">Financial Summary</h3>
            <p class="text-sm text-gray-500 mt-2">Outgoings (associate invoices) vs incomings (VTA invoices)</p>
        </a>
        <a href="{{ route('reports.patients-by-status') }}" class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-800">Patients by Status</h3>
            <p class="text-sm text-gray-500 mt-2">Active patients grouped by current status</p>
        </a>
        <a href="{{ route('reports.associate-activity') }}" class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-gray-800">Associate Activity</h3>
            <p class="text-sm text-gray-500 mt-2">Case notes and sign-offs per associate (6-month view)</p>
        </a>
        <a href="{{ route('reports.master-log') }}" class="rounded-xl border border-[#0092b4] bg-white p-6 hover:shadow-md transition">
            <h3 class="text-lg font-semibold" style="color:#0092b4;">Master Log</h3>
            <p class="text-sm text-gray-500 mt-2">All active patients with approved funding, expenses, and balance at a glance</p>
        </a>
    </div>
</x-app-layout>
