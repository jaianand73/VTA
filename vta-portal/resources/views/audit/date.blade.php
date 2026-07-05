@php $header = 'Date Audit'; @endphp
<x-app-layout>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Date Audit</h1>
            <p class="text-sm text-gray-500 mt-0.5">Everything that happened in a date range</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('audit.patient') }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">Patient Audit</a>
            <a href="{{ route('audit.associate') }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">Associate Audit</a>
        </div>
    </div>

    {{-- Date range form --}}
    <form method="GET" action="{{ route('audit.date') }}" class="flex flex-wrap gap-3 items-end mb-6">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border-gray-300 text-sm shadow-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border-gray-300 text-sm shadow-sm">
        </div>
        {{-- Quick range shortcuts --}}
        <div class="flex gap-1.5 items-end pb-0.5">
            @php
                $today = today()->toDateString();
                $shortcuts = [
                    'Today'    => [$today, $today],
                    'Yesterday'=> [now()->subDay()->toDateString(), now()->subDay()->toDateString()],
                    '7 Days'   => [now()->subDays(6)->toDateString(), $today],
                    '30 Days'  => [now()->subDays(29)->toDateString(), $today],
                    'This Month'=> [now()->startOfMonth()->toDateString(), $today],
                ];
            @endphp
            @foreach($shortcuts as $label => [$from, $to])
            @php $active = ($dateFrom === $from && $dateTo === $to); @endphp
            <a href="{{ route('audit.date', ['date_from'=>$from,'date_to'=>$to]) }}"
               class="text-xs px-2.5 py-1.5 rounded-lg border font-medium transition-colors"
               style="{{ $active ? 'background:#0092b4;color:#fff;border-color:#0092b4;' : 'background:#fff;color:#64748b;border-color:#e2e8f0;' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
        <button type="submit" style="background:#0092b4;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
            View
        </button>
    </form>

    @if($logs->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-10 text-center text-gray-400">
        <i class="fa-solid fa-calendar-xmark text-3xl mb-3 block"></i>
        No activity recorded between {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} and {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}.
    </div>
    @else

    {{-- Smart Summary --}}
    @php
        $rangeDays = \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1;
        $rangeLabel = $rangeDays === 1
            ? \Carbon\Carbon::parse($dateFrom)->format('d M Y')
            : \Carbon\Carbon::parse($dateFrom)->format('d M') . ' – ' . \Carbon\Carbon::parse($dateTo)->format('d M Y');
    @endphp
    <div class="rounded-xl border border-[#0092b4]/20 mb-5" style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);">
        <div class="px-5 py-4 border-b border-[#0092b4]/15 flex items-center gap-2">
            <i class="fa-solid fa-sparkles text-sm" style="color:#0092b4;"></i>
            <span class="font-semibold text-sm text-gray-800">Smart Summary</span>
            <span class="text-xs text-gray-500 ml-1">{{ $rangeLabel }} &middot; {{ $logs->count() }} events across {{ $summary['patientIds']->count() }} patient(s) &amp; {{ $summary['associateIds']->count() }} associate(s)</span>
        </div>
        <div class="px-5 py-4">
            {{-- Stat tiles --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                @php
                    $tiles = [
                        ['icon'=>'fa-user-plus','label'=>'New Patients','value'=>$summary['newPatients'],'color'=>'#0092b4'],
                        ['icon'=>'fa-envelope-open-text','label'=>'New Enquiries','value'=>$summary['newEnquiries'],'color'=>'#7c3aed'],
                        ['icon'=>'fa-clipboard-check','label'=>'Sessions','value'=>$summary['caseNotes'],'color'=>'#16a34a'],
                        ['icon'=>'fa-file-signature','label'=>'Signed Off','value'=>$summary['signedOff'],'color'=>'#16a34a'],
                        ['icon'=>'fa-arrow-right-arrow-left','label'=>'Status Changes','value'=>$summary['statusChanges'],'color'=>'#ea580c'],
                        ['icon'=>'fa-file-invoice-dollar','label'=>'Invoices','value'=>$summary['invoices'],'color'=>'#0369a1'],
                        ['icon'=>'fa-sterling-sign','label'=>'Funding Approved','value'=>$summary['fundingEvents'],'color'=>'#16a34a'],
                        ['icon'=>'fa-bolt','label'=>'Busiest Day','value'=>$summary['busiestDay'] ? \Carbon\Carbon::parse($summary['busiestDay'])->format('d M').' ('.$summary['busiestCount'].')' : '—','color'=>'#d97706'],
                    ];
                @endphp
                @foreach($tiles as $tile)
                <div class="rounded-lg bg-white border border-white/80 px-3 py-2.5 flex items-center gap-3 shadow-sm">
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

            {{-- Narrative paragraph --}}
            @php
                $narrative = [];
                if ($summary['newEnquiries'] > 0) $narrative[] = $summary['newEnquiries'] . ' new ' . \Illuminate\Support\Str::plural('enquiry', $summary['newEnquiries']) . ' received';
                if ($summary['newPatients'] > 0) $narrative[] = $summary['newPatients'] . ' new ' . \Illuminate\Support\Str::plural('patient', $summary['newPatients']) . ' registered';
                if ($summary['caseNotes'] > 0) $narrative[] = $summary['caseNotes'] . ' treatment ' . \Illuminate\Support\Str::plural('session', $summary['caseNotes']) . ' documented';
                if ($summary['signedOff'] > 0) $narrative[] = $summary['signedOff'] . ' ' . \Illuminate\Support\Str::plural('note', $summary['signedOff']) . ' signed off';
                if ($summary['fundingEvents'] > 0) $narrative[] = $summary['fundingEvents'] . ' funding ' . \Illuminate\Support\Str::plural('approval', $summary['fundingEvents']);
                if ($summary['invoices'] > 0) $narrative[] = $summary['invoices'] . ' ' . \Illuminate\Support\Str::plural('invoice', $summary['invoices']) . ' raised';
                if ($summary['statusChanges'] > 0) $narrative[] = $summary['statusChanges'] . ' status ' . \Illuminate\Support\Str::plural('update', $summary['statusChanges']);
            @endphp
            @if(count($narrative))
            <p class="text-sm text-gray-600 leading-relaxed">
                <i class="fa-solid fa-circle-info mr-1 opacity-50"></i>
                In this period: {{ implode(', ', $narrative) }}.
                @if($summary['busiestDay'])
                Busiest day was <strong>{{ \Carbon\Carbon::parse($summary['busiestDay'])->format('l d M') }}</strong> with {{ $summary['busiestCount'] }} events.
                @endif
            </p>
            @endif
        </div>
    </div>

    {{-- Timeline grouped by date --}}
    @php $currentDate = null; @endphp
    <div class="relative">
        <div class="absolute left-5 top-0 bottom-0 w-px" style="background:#e5e7eb;"></div>
        <div class="space-y-3">
            @foreach($logs as $log)
            @php
                $logDate = $log->occurred_at->format('d M Y');
                $meta    = \App\Models\ActivityLog::actionMeta($log->action);
            @endphp
            @if($logDate !== $currentDate)
                @php $currentDate = $logDate; @endphp
                <div class="flex gap-4 items-center">
                    <div class="w-10 flex-shrink-0"></div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest py-1">{{ $logDate }}</span>
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
                        @if($log->associate)
                        <span class="text-xs text-gray-500"><i class="fa-solid fa-user-nurse mr-1 opacity-50"></i>{{ $log->associate->name }}</span>
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
</x-app-layout>
