@php $header = 'Accounts'; @endphp
<x-app-layout>
    <div class="space-y-6">

        {{-- Tab bar --}}
        <div class="flex gap-1 rounded-xl bg-gray-100 p-1 w-fit">
            <a href="{{ route('accounts.index', ['tab' => 'vta']) }}"
               class="rounded-lg px-5 py-2 text-sm font-medium transition-colors {{ $tab === 'vta' ? 'bg-white text-[#0092b4] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-file-invoice mr-1.5"></i> VTA Invoices
            </a>
            <a href="{{ route('accounts.index', ['tab' => 'associate']) }}"
               class="rounded-lg px-5 py-2 text-sm font-medium transition-colors {{ $tab === 'associate' ? 'bg-white text-[#0092b4] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-file-invoice-dollar mr-1.5"></i> Associate Invoices
            </a>
            <a href="{{ route('accounts.index', ['tab' => 'referral-bills']) }}"
               class="relative rounded-lg px-5 py-2 text-sm font-medium transition-colors {{ $tab === 'referral-bills' ? 'bg-white text-[#0092b4] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-clipboard-list mr-1.5"></i> Referral Bills
                @if($billSummary['pending_count'] > 0)
                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-amber-100 px-1.5 py-0.5 text-xs font-medium text-amber-700">{{ $billSummary['pending_count'] }}</span>
                @endif
            </a>
        </div>

        {{-- ── VTA INVOICES TAB ─────────────────────────────── --}}
        @if($tab === 'vta')
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Invoices sent by VTA to funders</p>
            <a href="{{ route('vta-invoices.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                <i class="fa-solid fa-plus"></i> Create Invoice
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoiced (Month)</p>
                <p class="mt-1 text-xl font-bold text-gray-900">&pound;{{ number_format($vtaSummary['invoiced_this_month'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Paid (Month)</p>
                <p class="mt-1 text-xl font-bold text-green-600">&pound;{{ number_format($vtaSummary['paid_this_month'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</p>
                <p class="mt-1 text-xl font-bold text-amber-600">&pound;{{ number_format($vtaSummary['outstanding'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue</p>
                <p class="mt-1 text-xl font-bold text-{{ $vtaSummary['overdue_count'] > 0 ? 'red' : 'gray' }}-600">{{ $vtaSummary['overdue_count'] }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Recipient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($vtaInvoices as $inv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-[#0092b4]">
                            <a href="{{ route('vta-invoices.show', $inv) }}" class="hover:underline">{{ $inv->invoice_number }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->patient?->first_name }} {{ $inv->patient?->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->recipient_name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">&pound;{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->invoice_date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm {{ $inv->status == 'Overdue' ? 'text-red-600 font-medium' : 'text-gray-600' }}">{{ $inv->due_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @switch($inv->status)
                                    @case('Draft') bg-gray-100 text-gray-800 @break
                                    @case('Sent') bg-blue-100 text-blue-800 @break
                                    @case('Paid') bg-green-100 text-green-800 @break
                                    @case('Overdue') bg-red-100 text-red-800 @break
                                    @case('Cancelled') bg-gray-100 text-gray-500 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch">{{ $inv->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('vta-invoices.show', $inv) }}" class="text-[#0092b4] hover:text-[#007a9a]"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No VTA invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $vtaInvoices->appends(request()->except('vta_page'))->links() }}</div>
        @endif

        {{-- ── ASSOCIATE INVOICES TAB ───────────────────────── --}}
        @if($tab === 'associate')
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Invoices received from associates</p>
            <a href="{{ route('associate-invoices.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                <i class="fa-solid fa-plus"></i> Log Invoice
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Received This Month</p>
                <p class="mt-1 text-xl font-bold text-gray-900">&pound;{{ number_format($assocSummary['received_this_month'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Paid This Month</p>
                <p class="mt-1 text-xl font-bold text-green-600">&pound;{{ number_format($assocSummary['paid_this_month'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue</p>
                <p class="mt-1 text-xl font-bold text-{{ $assocSummary['overdue_count'] > 0 ? 'red' : 'gray' }}-600">{{ $assocSummary['overdue_count'] }}</p>
            </div>
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
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('associate-invoices.show', $inv) }}" class="text-[#0092b4] hover:text-[#007a9a]"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No associate invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $associateInvoices->appends(request()->except('assoc_page'))->links() }}</div>
        @endif

        {{-- ── REFERRAL BILLS TAB ───────────────────────────── --}}
        @if($tab === 'referral-bills')
        <p class="text-sm text-gray-500">Bills raised during the referral/assessment stage (pre-patient)</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Billed This Month</p>
                <p class="mt-1 text-xl font-bold text-gray-900">&pound;{{ number_format($billSummary['total_this_month'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</p>
                <p class="mt-1 text-xl font-bold text-green-600">&pound;{{ number_format($billSummary['paid_total'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Payment</p>
                <p class="mt-1 text-xl font-bold text-amber-600">&pound;{{ number_format($billSummary['pending_total'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Awaiting Approval</p>
                <p class="mt-1 text-xl font-bold text-{{ $billSummary['pending_count'] > 0 ? 'amber' : 'gray' }}-600">{{ $billSummary['pending_count'] }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('accounts.index', ['tab' => 'referral-bills']) }}" class="flex gap-3 items-end">
            <input type="hidden" name="tab" value="referral-bills">
            <div>
                <label class="block text-xs font-medium text-gray-500">Associate</label>
                <select name="bill_associate_id" class="mt-1 block rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    <option value="">All Associates</option>
                    @foreach($associates as $a)
                    <option value="{{ $a->id }}" {{ request('bill_associate_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500">Status</label>
                <select name="bill_status" class="mt-1 block rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('bill_status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Paid" {{ request('bill_status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Unpaid" {{ request('bill_status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Filter</button>
            @if(request('bill_associate_id') || request('bill_status'))
            <a href="{{ route('accounts.index', ['tab' => 'referral-bills']) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Clear</a>
            @endif
        </form>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Associate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Bill Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($referralBills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-[#0092b4]">
                            <a href="{{ route('referrals.show', $bill->referral) }}" class="hover:underline font-mono">{{ $bill->referral->referral_ref }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bill->referral->patient_first_name }} {{ $bill->referral->patient_last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bill->referral->associate?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bill->bill_date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">&pound;{{ number_format($bill->amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $bill->status === 'Paid' ? 'bg-green-100 text-green-800' : ($bill->status === 'Pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ $bill->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $bill->notes ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($bill->status === 'Pending')
                            <form method="POST" action="{{ route('referrals.bills.mark-paid', $bill) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs rounded-lg bg-green-600 px-2.5 py-1 text-white hover:bg-green-700">Mark Paid</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No referral bills found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $referralBills->appends(request()->except('bill_page'))->links() }}</div>
        @endif

    </div>
</x-app-layout>
