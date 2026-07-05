@php $header = 'Financial Summary'; @endphp
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Incoming (VTA Invoices)</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Invoiced</span><span class="font-medium">£{{ number_format($vtaInvoiced, 2) }}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Paid</span><span class="font-medium text-green-600">£{{ number_format($vtaPaid, 2) }}</span></div>
                <div class="flex justify-between text-sm border-t border-gray-100 pt-2"><span class="font-medium">Outstanding</span><span class="font-medium text-amber-600">£{{ number_format($vtaInvoiced - $vtaPaid, 2) }}</span></div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Outgoing (Associate Invoices)</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Invoiced</span><span class="font-medium">£{{ number_format($associateInvoiced, 2) }}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Paid</span><span class="font-medium text-green-600">£{{ number_format($associatePaid, 2) }}</span></div>
                <div class="flex justify-between text-sm border-t border-gray-100 pt-2"><span class="font-medium">Outstanding</span><span class="font-medium text-amber-600">£{{ number_format($associateInvoiced - $associatePaid, 2) }}</span></div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Assessment Costs (Referral Bills)</h3>
            <p class="text-xs text-gray-400 mb-3">Bills raised during the referral/assessment stage</p>
            <div class="space-y-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Billed</span><span class="font-medium">£{{ number_format($referralBillsTotal, 2) }}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Paid</span><span class="font-medium text-green-600">£{{ number_format($referralBillsPaid, 2) }}</span></div>
                <div class="flex justify-between text-sm border-t border-gray-100 pt-2"><span class="font-medium">Pending</span><span class="font-medium text-amber-600">£{{ number_format($referralBillsTotal - $referralBillsPaid, 2) }}</span></div>
            </div>
        </div>
    </div>
</x-app-layout>
