@php $header = 'Associate Audit'; @endphp
@push('styles')
<style>
.sortable-tile { cursor:grab; user-select:none; transition:box-shadow .15s, opacity .15s; }
.sortable-tile:active { cursor:grabbing; }
.sortable-ghost  { opacity:.35; }
.sortable-chosen { box-shadow:0 0 0 2px #7c3aed, 0 4px 16px rgba(124,58,237,.2); border-radius:12px; }
.sortable-drag   { opacity:.9; box-shadow:0 8px 24px rgba(0,0,0,.15); border-radius:12px; }
</style>
@endpush
<x-app-layout>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Associate Audit</h1>
            <p class="text-sm text-gray-500 mt-0.5">Full activity, workload, and compliance for an associate</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('audit.date') }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">Date Audit</a>
            <a href="{{ route('audit.patient') }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">Patient Audit</a>
        </div>
    </div>

    <form method="GET" action="{{ route('audit.associate') }}" class="flex flex-wrap gap-3 items-end mb-6">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Select Associate</label>
            <select name="associate_id" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                <option value="">— Choose an associate —</option>
                @foreach($associates as $a)
                    <option value="{{ $a->id }}" @selected(request('associate_id') == $a->id)>
                        {{ $a->name }}{{ $a->is_active ? '' : ' (inactive)' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="date-range-fields" class="{{ $allTime ? 'hidden' : 'flex gap-2' }}">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border-gray-300 text-sm shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border-gray-300 text-sm shadow-sm">
            </div>
        </div>

        <div class="flex items-end gap-2">
            <label class="flex items-center gap-1.5 cursor-pointer select-none pb-1">
                <input type="checkbox" name="all_time" value="1" @checked($allTime)
                       onchange="document.getElementById('date-range-fields').classList.toggle('hidden', this.checked); document.getElementById('date-range-fields').classList.toggle('flex', !this.checked);"
                       class="rounded border-gray-300 text-[#0092b4]">
                <span class="text-sm text-gray-600 font-medium">All Time</span>
            </label>
            <button type="submit" style="background:#0092b4;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                View
            </button>
        </div>
    </form>

    @if($associate)

        {{-- Associate profile card --}}
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 mb-4">
            <div class="flex flex-wrap items-start gap-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl flex-shrink-0"
                     style="background:#7c3aed;">
                    {{ substr($associate->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 text-lg">{{ $associate->name }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $associate->region ?? 'No region' }}
                        @if($associate->speciality)
                            &middot; {{ $associate->speciality }}
                        @endif
                        @if($associate->session_rate)
                            &middot; £{{ number_format($associate->session_rate, 2) }}/session
                        @endif
                    </p>
                    @if($associate->created_at)
                        <p class="text-xs text-gray-400 mt-0.5">
                            <i class="fa-solid fa-calendar-plus mr-1"></i>
                            Inducted {{ \Carbon\Carbon::parse($associate->created_at)->format('d M Y') }}
                            ({{ \Carbon\Carbon::parse($associate->created_at)->diffForHumans() }})
                        </p>
                    @endif
                </div>
                <a href="{{ route('settings.associates.show', $associate) }}"
                   class="text-sm font-medium hover:underline flex-shrink-0" style="color:#7c3aed;">
                    View Profile <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            @if($associate->complianceDocuments->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($associate->complianceDocuments as $doc)
                        @php
                            $isExp  = $doc->isExpired();
                            $isSoon = !$isExp && $doc->isExpiringSoon();
                            $bg     = $isExp ? '#fef2f2' : ($isSoon ? '#fffbeb' : '#f0fdf4');
                            $color  = $isExp ? '#dc2626' : ($isSoon ? '#d97706' : '#16a34a');
                            $icon   = $isExp ? 'fa-circle-xmark' : ($isSoon ? 'fa-triangle-exclamation' : 'fa-circle-check');
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium border"
                              style="background:{{ $bg }};color:{{ $color }};border-color:{{ $color }}22;">
                            <i class="fa-solid {{ $icon }}"></i>
                            {{ $doc->document_type }}
                            @if($doc->expiry_date)
                                — {{ $doc->expiry_date->format('d M Y') }}
                            @endif
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Smart Summary --}}
        @if(!empty($summary))
            @php
                $sumRangeLabel = $allTime
                    ? 'All time'
                    : (\Carbon\Carbon::parse($dateFrom)->format('d M') . ' – ' . \Carbon\Carbon::parse($dateTo)->format('d M Y'));
            @endphp
            <div class="rounded-xl border border-purple-200 mb-4" style="background:linear-gradient(135deg,#faf5ff 0%,#ede9fe 100%);">
                <div class="px-5 py-3 border-b border-purple-100 flex items-center gap-2">
                    <i class="fa-solid fa-sparkles text-sm" style="color:#7c3aed;"></i>
                    <span class="font-semibold text-sm text-gray-800">Smart Summary</span>
                    <span class="text-xs text-gray-500 ml-1">{{ $sumRangeLabel }} &middot; {{ $rangeLogs->count() }} events</span>
                    <button id="gs-reset" class="ml-auto text-xs text-gray-400 hover:text-gray-600 flex items-center gap-1" title="Reset layout">
                        <i class="fa-solid fa-rotate-left"></i> Reset layout
                    </button>
                </div>
                <div class="px-5 py-4">
                    @php
                        $sumTiles = [
                            ['icon' => 'fa-clipboard',           'label' => 'Sessions',         'value' => $summary['sessions'],       'color' => '#7c3aed'],
                            ['icon' => 'fa-file-signature',      'label' => 'Signed Off',        'value' => $summary['signedOff'],      'color' => '#16a34a'],
                            ['icon' => 'fa-users',               'label' => 'Patients Worked',   'value' => $summary['patientsWorked'], 'color' => '#0092b4'],
                            ['icon' => 'fa-calendar-check',      'label' => 'Appointments',      'value' => $summary['appointments'],   'color' => '#ea580c'],
                        ];
                        if ($summary['invoiceTotal'] > 0) {
                            $sumTiles[] = ['icon' => 'fa-file-invoice-dollar', 'label' => 'Invoiced', 'value' => '£' . number_format($summary['invoiceTotal']), 'color' => '#0369a1'];
                        }
                        if ($summary['invoicePaid'] > 0) {
                            $sumTiles[] = ['icon' => 'fa-sterling-sign', 'label' => 'Paid', 'value' => '£' . number_format($summary['invoicePaid']), 'color' => '#16a34a'];
                        }
                        if ($summary['complianceDocs'] > 0) {
                            $sumTiles[] = ['icon' => 'fa-shield-check', 'label' => 'Compliance Docs', 'value' => $summary['complianceDocs'], 'color' => '#d97706'];
                        }
                    @endphp
                    <div id="gs-summary" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:4px;">
                        @foreach($sumTiles as $i => $tile)
                            <div class="sortable-tile" data-id="sum-{{ $i }}" style="background:#fff;border:1px solid #f1f5f9;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);flex:1 1 calc(25% - 8px);min-width:140px;">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                     style="background:{{ $tile['color'] }}18;">
                                    <i class="fa-solid {{ $tile['icon'] }} text-xs" style="color:{{ $tile['color'] }};"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-800 leading-tight">{{ $tile['value'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $tile['label'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @php
                        $nArr = [];
                        if ($summary['sessions'] > 0)      $nArr[] = $summary['sessions'] . ' treatment ' . \Illuminate\Support\Str::plural('session', $summary['sessions']) . ' delivered';
                        if ($summary['signedOff'] > 0)     $nArr[] = $summary['signedOff'] . ' ' . \Illuminate\Support\Str::plural('note', $summary['signedOff']) . ' signed off';
                        if ($summary['patientsWorked'] > 0) $nArr[] = $summary['patientsWorked'] . ' ' . \Illuminate\Support\Str::plural('patient', $summary['patientsWorked']) . ' seen';
                        if ($summary['invoicePaid'] > 0)   $nArr[] = '£' . number_format($summary['invoicePaid']) . ' received in payments';
                        if ($summary['topPatient'])         $nArr[] = 'most activity with ' . $summary['topPatient']->first_name . ' ' . $summary['topPatient']->last_name;
                    @endphp
                    @if(count($nArr) > 0)
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <i class="fa-solid fa-circle-info mr-1 opacity-50"></i>
                            {{ ucfirst(implode(', ', $nArr)) }}.
                        </p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Stats row --}}
        @if(!empty($stats))
            @php
                $statTiles = [
                    ['label'=>'Active Patients',      'value'=>$stats['activePatients'],                        'color'=>'#0092b4', 'icon'=>'fa-user-clock'],
                    ['label'=>'Completed',            'value'=>$stats['completedPatients'],                     'color'=>'#16a34a', 'icon'=>'fa-user-check'],
                    ['label'=>'Sessions This Month',  'value'=>$stats['sessionsThisMonth'],                     'color'=>'#374151', 'icon'=>'fa-calendar-day'],
                    ['label'=>'Pending Invoices',     'value'=>$stats['pendingInvoices'],                       'color'=>$stats['pendingInvoices'] > 0 ? '#ea580c' : '#374151', 'icon'=>'fa-file-invoice'],
                    ['label'=>'Total Paid',           'value'=>'£'.number_format($stats['totalEarned'], 0),     'color'=>'#16a34a', 'icon'=>'fa-sterling-sign'],
                    ['label'=>'Referral Sessions',    'value'=>$stats['referralSessionsCount'],                 'color'=>'#059669', 'icon'=>'fa-file-medical'],
                    ['label'=>'Bills Pending',        'value'=>$stats['referralBillsPending'],                  'color'=>$stats['referralBillsPending'] > 0 ? '#d97706' : '#374151', 'icon'=>'fa-receipt'],
                    ['label'=>'Bills Paid (Ref.)',    'value'=>'£'.number_format($stats['referralBillsPaid'], 0),'color'=>'#059669', 'icon'=>'fa-sterling-sign'],
                ];
            @endphp
            <div id="gs-stats" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px;">
                @foreach($statTiles as $i => $st)
                    <div class="sortable-tile" data-id="stat-{{ $i }}"
                         style="background:#fff;border:1px solid #f1f5f9;border-radius:12px;padding:14px 12px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.06);flex:1 1 calc(20% - 8px);min-width:120px;">
                        <p style="font-size:22px;font-weight:700;color:{{ $st['color'] }};line-height:1.2;">{{ $st['value'] }}</p>
                        <p style="font-size:11px;color:#6b7280;margin-top:3px;">{{ $st['label'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Upcoming appointments --}}
        @if($upcoming->isNotEmpty())
            <div class="rounded-xl border p-4 mb-5" style="border-color:#c4b5fd;background:#faf5ff;">
                <h3 class="text-sm font-semibold mb-3" style="color:#6d28d9;">
                    <i class="fa-solid fa-calendar-days mr-1"></i> Upcoming Schedule (Next 14 Days)
                </h3>
                @php $lastDay = null; @endphp
                @foreach($upcoming as $appt)
                    @php $apptDay = \Carbon\Carbon::parse($appt->scheduled_at)->format('D d M Y'); @endphp
                    @if($apptDay !== $lastDay)
                        @php $lastDay = $apptDay; @endphp
                        <p class="text-xs font-semibold text-purple-400 uppercase tracking-wide mt-2 mb-1">{{ $apptDay }}</p>
                    @endif
                    <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2 border border-purple-100 mb-1">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $appt->first_name }} {{ $appt->last_name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('H:i') }}
                                @if($appt->duration_minutes)
                                    &middot; {{ $appt->duration_minutes }} min
                                @endif
                                @if($appt->location)
                                    &middot; {{ $appt->location }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs rounded-full px-2 py-0.5" style="background:#ede9fe;color:#6d28d9;">Scheduled</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 mb-5 text-sm text-gray-400">
                <i class="fa-solid fa-calendar-xmark mr-2"></i> No upcoming appointments in the next 14 days.
            </div>
        @endif

        {{-- Referral Sessions section --}}
        @if($referralSessions->isNotEmpty())
        <div class="rounded-xl border mb-5 overflow-hidden" style="border-color:#a7f3d0;">
            <div class="px-5 py-3 flex items-center justify-between" style="background:#ecfdf5;">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-medical text-sm" style="color:#059669;"></i>
                    <span class="font-semibold text-sm text-gray-800">Referral Assessment Sessions</span>
                    <span class="text-xs text-gray-500">{{ $referralSessions->count() }} sessions across {{ $referralSessions->pluck('referral_id')->unique()->count() }} referrals</span>
                </div>
            </div>
            @php $currentRef = null; @endphp
            <div class="divide-y divide-gray-100 bg-white">
                @foreach($referralSessions->sortBy('session_date') as $sess)
                @if($sess->referral_id !== $currentRef)
                    @php $currentRef = $sess->referral_id; @endphp
                    <div class="px-5 py-2 bg-gray-50 flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 font-mono">{{ $sess->referral->referral_ref }}</span>
                        <span class="text-xs text-gray-400">{{ $sess->referral->patient_first_name }} {{ $sess->referral->patient_last_name }}</span>
                        <a href="{{ route('referrals.show', $sess->referral) }}" class="text-xs hover:underline" style="color:#059669;">View Referral</a>
                    </div>
                @endif
                <div class="flex items-center justify-between px-5 py-2.5 text-sm hover:bg-gray-50">
                    <div>
                        <span class="font-medium text-gray-700">{{ $sess->activityType?->name ?? 'Session' }}</span>
                        @if($sess->location) <span class="text-xs text-gray-400 ml-1">&middot; {{ $sess->location }}</span> @endif
                        @if($sess->notes) <p class="text-xs text-gray-400 mt-0.5 max-w-lg truncate">{{ $sess->notes }}</p> @endif
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        <p class="text-xs text-gray-500">{{ $sess->session_date ? \Carbon\Carbon::parse($sess->session_date)->format('d M Y') : '—' }}</p>
                        @if($sess->duration_minutes)<p class="text-xs text-gray-400">{{ $sess->duration_minutes }} min</p>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Timeline --}}
        @if($logs->isEmpty())
            <div class="rounded-xl border border-gray-100 bg-white p-10 text-center text-gray-400">
                <i class="fa-solid fa-inbox text-3xl mb-3 block"></i>
                No activity recorded {{ $allTime ? 'for this associate yet.' : 'in the selected date range.' }}
            </div>
        @else
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                    Activity Timeline
                    @if($allTime)
                        <span class="ml-2 text-xs font-normal normal-case rounded-full px-2 py-0.5" style="background:#ede9fe;color:#6d28d9;">All Time — {{ $logs->count() }} events</span>
                    @else
                        <span class="ml-2 text-xs font-normal normal-case text-gray-400">{{ $logs->count() }} events</span>
                    @endif
                </h3>
            </div>

            @php $currentDate = null; $currentMonth = null; @endphp
            <div class="relative">
                <div class="absolute left-5 top-0 bottom-0 w-px" style="background:#e5e7eb;"></div>
                <div class="space-y-3">
                    @foreach($logs as $log)
                        @php
                            $logDate  = $log->occurred_at->format('d M Y');
                            $logMonth = $log->occurred_at->format('M Y');
                            $meta     = \App\Models\ActivityLog::actionMeta($log->action);
                        @endphp
                        @if($logMonth !== $currentMonth)
                            @php $currentMonth = $logMonth; $currentDate = null; @endphp
                            <div class="flex gap-4 items-center">
                                <div class="w-10 flex-shrink-0"></div>
                                <div class="flex-1 border-t border-gray-200 pt-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $logMonth }}</span>
                                </div>
                            </div>
                        @endif
                        @if($logDate !== $currentDate)
                            @php $currentDate = $logDate; @endphp
                            <div class="flex gap-4 items-center">
                                <div class="w-10 flex-shrink-0"></div>
                                <span class="text-xs font-semibold text-gray-400 py-0.5">{{ $logDate }}</span>
                            </div>
                        @endif
                        <div class="flex gap-4 items-start">
                            <div class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                                 style="background:#f8fafc;border:2px solid {{ $meta['color'] }};">
                                <i class="fa-solid {{ $meta['icon'] }} text-sm" style="color:{{ $meta['color'] }};"></i>
                            </div>
                            <div class="flex-1 rounded-xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-800">{{ $log->description }}</p>
                                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $log->occurred_at->format('H:i') }}</span>
                                </div>
                                <div class="flex flex-wrap gap-3 mt-1.5">
                                    @if($log->user)
                                        <span class="text-xs text-gray-500"><i class="fa-solid fa-user mr-1 opacity-50"></i>{{ $log->user->name }}</span>
                                    @endif
                                    @if($log->patient)
                                        <a href="{{ route('patients.show', $log->patient) }}" class="text-xs hover:underline" style="color:#0092b4;">
                                            <i class="fa-solid fa-person-walking-arrow-right mr-1 opacity-60"></i>{{ $log->patient->first_name }} {{ $log->patient->last_name }}
                                        </a>
                                    @endif
                                    <span class="text-xs rounded-full px-2 py-0.5" style="background:#f1f5f9;color:#64748b;">{{ str_replace('_', ' ', $log->subject_type) }}</span>
                                </div>
                                @if($log->metadata && isset($log->metadata['from']))
                                    <p class="text-xs mt-1 text-gray-400">{{ $log->metadata['from'] }} → {{ $log->metadata['to'] }}</p>
                                @endif
                                @if($log->metadata && isset($log->metadata['amount']))
                                    <p class="text-xs mt-1 font-medium" style="color:#16a34a;">£{{ number_format($log->metadata['amount'], 2) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif

    @if(!$associate && request('associate_id'))
        <div class="rounded-xl border border-red-100 bg-red-50 p-6 text-center text-red-600">Associate not found.</div>
    @endif

</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const STORAGE_KEY = 'vta_audit_assoc_order_{{ request("associate_id") ?? "0" }}';

    function getOrder(el) {
        return [...el.querySelectorAll('[data-id]')].map(t => t.dataset.id);
    }

    function applyOrder(el, order) {
        if (!order || !order.length) return;
        const items = {};
        el.querySelectorAll('[data-id]').forEach(t => items[t.dataset.id] = t);
        order.forEach(id => { if (items[id]) el.appendChild(items[id]); });
    }

    function saveOrders() {
        try {
            const data = {
                summary: getOrder(document.getElementById('gs-summary') || { querySelectorAll: () => [] }),
                stats:   getOrder(document.getElementById('gs-stats')   || { querySelectorAll: () => [] }),
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch(e) {}
    }

    function loadOrders() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            const data = JSON.parse(raw);
            const sumEl   = document.getElementById('gs-summary');
            const statsEl = document.getElementById('gs-stats');
            if (sumEl   && data.summary) applyOrder(sumEl,   data.summary);
            if (statsEl && data.stats)   applyOrder(statsEl, data.stats);
        } catch(e) {}
    }

    const sortableOpts = {
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: saveOrders,
    };

    document.addEventListener('DOMContentLoaded', function () {
        loadOrders();

        const sumEl   = document.getElementById('gs-summary');
        const statsEl = document.getElementById('gs-stats');
        if (sumEl)   Sortable.create(sumEl,   sortableOpts);
        if (statsEl) Sortable.create(statsEl, sortableOpts);

        const resetBtn = document.getElementById('gs-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}
                location.reload();
            });
        }
    });
})();
</script>
@endpush
