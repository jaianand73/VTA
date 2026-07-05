@php $header = 'Patient Audit'; @endphp
<x-app-layout>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Patient Audit</h1>
            <p class="text-sm text-gray-500 mt-0.5">Full journey from first enquiry to present day</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('audit.date') }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">Date Audit</a>
            <a href="{{ route('audit.associate') }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">Associate Audit</a>
        </div>
    </div>

    <form method="GET" action="{{ route('audit.patient') }}" class="flex flex-wrap gap-3 items-end mb-6">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Select Patient</label>
            <select name="patient_id" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                <option value="">— Choose a patient —</option>
                @foreach($patients as $p)
                <option value="{{ $p->id }}" @selected(request('patient_id') == $p->id)>
                    {{ $p->last_name }}, {{ $p->first_name }} ({{ $p->status }})
                </option>
                @endforeach
            </select>
        </div>

        {{-- Date range (hidden when all_time) --}}
        <div id="pat-date-range" class="{{ $allTime ? 'hidden' : 'flex gap-2' }}">
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
                       onchange="var dr=document.getElementById('pat-date-range'); dr.classList.toggle('hidden',this.checked); dr.classList.toggle('flex',!this.checked);"
                       class="rounded border-gray-300 text-[#0092b4]">
                <span class="text-sm text-gray-600 font-medium">All Up to Date</span>
            </label>
            <button type="submit" style="background:#0092b4;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                View Journey
            </button>
        </div>
    </form>

    @if($patient)

    {{-- Patient header card --}}
    <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 mb-4 flex flex-wrap items-center gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
             style="background:#0092b4;">
            {{ substr($patient->first_name,0,1) }}{{ substr($patient->last_name,0,1) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-base">{{ $patient->first_name }} {{ $patient->last_name }}</p>
            <p class="text-sm text-gray-500">
                {{ $patient->status }}
                @if($patient->location) &middot; {{ $patient->location }}@endif
                @if($patient->caseManager) &middot; {{ $patient->caseManager->name ?? ($patient->caseManager->first_name . ' ' . $patient->caseManager->last_name) }}@endif
            </p>
        </div>

        {{-- Journey stage progress bar --}}
        @php
            $stages = ['Enquiry','Assessment','Funding','Treatment','Invoiced','Closed'];
            $statusMap = [
                'Enquiry Logged'=>0,'Response Sent'=>0,'Awaiting LOI'=>0,'LOI Received'=>0,
                'Assessment Scheduled'=>1,'Assessment Completed'=>1,
                'Report Drafted'=>1,'Report Sent'=>1,'Cost Estimation Sent'=>1,
                'Awaiting Funding Approval'=>2,'Funding Approved'=>2,
                'Treatment Active'=>3,'On Hold'=>3,'Awaiting Further Funding'=>3,
                'Discharged'=>4,'Case Closed'=>5,'Not Proceeding'=>5,
            ];
            $stageIdx = $statusMap[$patient->status] ?? 0;
        @endphp
        <div class="flex items-center flex-shrink-0">
            @foreach($stages as $i => $stage)
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                         style="background:{{ $i <= $stageIdx ? '#0092b4' : '#e5e7eb' }};color:{{ $i <= $stageIdx ? '#fff' : '#9ca3af' }};">
                        {{ $i + 1 }}
                    </div>
                    <span class="mt-0.5 whitespace-nowrap" style="color:{{ $i <= $stageIdx ? '#0092b4' : '#9ca3af' }};font-size:9px;">{{ $stage }}</span>
                </div>
                @if(!$loop->last)
                <div class="w-5 h-0.5 mb-3" style="background:{{ $i < $stageIdx ? '#0092b4' : '#e5e7eb' }};"></div>
                @endif
            </div>
            @endforeach
        </div>

        <a href="{{ route('patients.show', $patient) }}" class="ml-auto text-sm font-medium hover:underline flex-shrink-0" style="color:#0092b4;">
            Open Record <i class="fa-solid fa-arrow-right ml-1"></i>
        </a>
    </div>

    {{-- Referral Stage Journey --}}
    @if($referral)
    @php
        $milestones = [
            ['label' => 'Enquiry logged',       'date' => $referral->enquiry?->enquiry_date,        'icon' => 'fa-circle-question', 'color' => '#d97706'],
            ['label' => 'Promoted to referral', 'date' => $referral->created_at?->toDateString(),   'icon' => 'fa-file-medical',    'color' => '#0092b4'],
            ['label' => 'Go-ahead received',    'date' => $referral->visit_approved_date,            'icon' => 'fa-circle-check',    'color' => '#059669'],
            ['label' => 'Proposal submitted',   'date' => $referral->proposal_submitted_date,        'icon' => 'fa-paper-plane',     'color' => '#7c3aed'],
            ['label' => 'Proposal approved',    'date' => $referral->proposal_approved_date,         'icon' => 'fa-thumbs-up',       'color' => '#16a34a'],
            ['label' => 'Converted to patient', 'date' => $patient->created_at?->toDateString(),     'icon' => 'fa-user-plus',       'color' => '#0092b4'],
        ];
    @endphp
    <div class="rounded-xl border mb-4 overflow-hidden" style="border-color:#d1fae5;">
        <div class="px-5 py-3 flex items-center justify-between" style="background:#ecfdf5;">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-file-medical text-sm" style="color:#059669;"></i>
                <span class="font-semibold text-sm text-gray-800">Referral Stage</span>
                <span class="text-xs text-gray-500 font-mono">{{ $referral->referral_ref }}</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" style="background:#d1fae5;color:#065f46;">{{ $referral->status }}</span>
            </div>
            <a href="{{ route('referrals.show', $referral) }}" class="text-xs font-medium hover:underline" style="color:#059669;">
                View Referral <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        {{-- Milestone timeline --}}
        <div class="px-5 py-4 bg-white">
            <div class="flex flex-wrap gap-x-0 gap-y-2">
                @foreach($milestones as $i => $m)
                @if($m['date'])
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                             style="background:{{ $m['color'] }}18;border:2px solid {{ $m['color'] }};">
                            <i class="fa-solid {{ $m['icon'] }} text-xs" style="color:{{ $m['color'] }};"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-700 mt-1 whitespace-nowrap">{{ $m['label'] }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($m['date'])->format('d M Y') }}</p>
                    </div>
                    @if(!$loop->last)
                    <div class="w-8 h-px mx-1 mb-8 flex-shrink-0" style="background:#d1fae5;"></div>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Activity counts --}}
        <div class="px-5 py-3 border-t grid grid-cols-2 sm:grid-cols-4 gap-3" style="border-color:#d1fae5;background:#f9fafb;">
            @php
                $actTiles = [
                    ['label'=>'Sessions',      'value'=>$referral->sessions->count(),      'icon'=>'fa-clipboard',         'color'=>'#059669'],
                    ['label'=>'Bills raised',  'value'=>$referral->bills->count(),          'icon'=>'fa-receipt',           'color'=>'#0369a1'],
                    ['label'=>'Comms',         'value'=>$referral->communications->count(), 'icon'=>'fa-comments',          'color'=>'#7c3aed'],
                    ['label'=>'Documents',     'value'=>$referral->documents->count(),      'icon'=>'fa-file-lines',        'color'=>'#d97706'],
                ];
            @endphp
            @foreach($actTiles as $t)
            <div class="flex items-center gap-2 rounded-lg bg-white border border-gray-100 px-3 py-2">
                <i class="fa-solid {{ $t['icon'] }} text-sm" style="color:{{ $t['color'] }};"></i>
                <div>
                    <p class="text-base font-bold text-gray-800 leading-tight">{{ $t['value'] }}</p>
                    <p class="text-xs text-gray-500">{{ $t['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Referral sessions mini-list --}}
        @if($referral->sessions->isNotEmpty())
        <div class="px-5 py-3 border-t" style="border-color:#d1fae5;">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Assessment Sessions</p>
            <div class="space-y-1.5">
                @foreach($referral->sessions->sortBy('session_date') as $sess)
                <div class="flex items-center justify-between rounded-lg bg-white border border-gray-100 px-3 py-2 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">{{ $sess->activityType?->name ?? 'Session' }}</span>
                        @if($sess->location) <span class="text-gray-400 text-xs ml-1">&middot; {{ $sess->location }}</span> @endif
                        @if($sess->notes) <p class="text-xs text-gray-400 mt-0.5 truncate max-w-sm">{{ $sess->notes }}</p> @endif
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0 ml-3">
                        {{ $sess->session_date ? \Carbon\Carbon::parse($sess->session_date)->format('d M Y') : '—' }}
                        @if($sess->duration_minutes) &middot; {{ $sess->duration_minutes }}min @endif
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Referral bills mini-list --}}
        @if($referral->bills->isNotEmpty())
        <div class="px-5 py-3 border-t" style="border-color:#d1fae5;">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Assessment Bills</p>
            <div class="space-y-1.5">
                @foreach($referral->bills->sortBy('bill_date') as $bill)
                <div class="flex items-center justify-between rounded-lg bg-white border border-gray-100 px-3 py-2 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">£{{ number_format($bill->amount, 2) }}</span>
                        @if($bill->notes) <span class="text-xs text-gray-400 ml-1">{{ $bill->notes }}</span> @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $bill->bill_date?->format('d M Y') }}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $bill->status === 'Paid' ? 'bg-green-100 text-green-700' : ($bill->status === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                            {{ $bill->status }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Smart Summary --}}
    @if(!empty($summary))
    @php
        $activeFunding = $patient->fundingCycles->where('is_active', true)->first();
        $rangeLabel = $allTime ? 'All time' :
            (\Carbon\Carbon::parse($dateFrom)->format('d M') . ' – ' . \Carbon\Carbon::parse($dateTo)->format('d M Y'));
    @endphp
    <div class="rounded-xl border border-[#0092b4]/20 mb-4" style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);">
        <div class="px-5 py-3 border-b border-[#0092b4]/15 flex items-center gap-2">
            <i class="fa-solid fa-sparkles text-sm" style="color:#0092b4;"></i>
            <span class="font-semibold text-sm text-gray-800">Smart Summary</span>
            <span class="text-xs text-gray-500 ml-1">{{ $rangeLabel }} &middot; {{ $rangeLogs->count() }} events</span>
        </div>
        <div class="px-5 py-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                @php $sumTiles = [
                    ['icon'=>'fa-clipboard','label'=>'Sessions','value'=>$summary['sessions'],'color'=>'#0092b4'],
                    ['icon'=>'fa-file-signature','label'=>'Signed Off','value'=>$summary['signedOff'],'color'=>'#16a34a'],
                    ['icon'=>'fa-arrow-right-arrow-left','label'=>'Status Changes','value'=>$summary['statusChanges'],'color'=>'#ea580c'],
                    ['icon'=>'fa-comments','label'=>'Communications','value'=>$summary['comms'],'color'=>'#7c3aed'],
                ];
                if ($summary['fundingApproved'] > 0) {
                    $sumTiles[] = ['icon'=>'fa-sterling-sign','label'=>'Funding','value'=>'£'.number_format($summary['fundingApproved']),'color'=>'#16a34a'];
                }
                if ($summary['invoiced'] > 0) {
                    $sumTiles[] = ['icon'=>'fa-file-invoice-dollar','label'=>'Invoiced','value'=>'£'.number_format($summary['invoiced']),'color'=>'#0369a1'];
                }
                $sumTiles[] = ['icon'=>'fa-calendar-days','label'=>'Days in System','value'=>$summary['spanDays'],'color'=>'#d97706']; @endphp
                @foreach($sumTiles as $tile)
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

            {{-- Stage transitions --}}
            @if(!empty($summary['transitions']))
            <div class="flex flex-wrap gap-2 mt-1">
                <span class="text-xs text-gray-500 self-center">Stage transitions:</span>
                @foreach($summary['transitions'] as $t)
                <span class="text-xs rounded-full px-2.5 py-0.5 font-medium" style="background:#fff7ed;color:#c2410c;">{{ $t }}</span>
                @endforeach
            </div>
            @endif

            {{-- Narrative --}}
            @php
                $nArr = [];
                if ($summary['sessions'] > 0) $nArr[] = $summary['sessions'] . ' treatment ' . \Illuminate\Support\Str::plural('session', $summary['sessions']);
                if ($summary['signedOff'] > 0) $nArr[] = $summary['signedOff'] . ' signed off';
                if ($summary['comms'] > 0) $nArr[] = $summary['comms'] . ' ' . \Illuminate\Support\Str::plural('communication', $summary['comms']);
                if ($summary['fundingApproved'] > 0) $nArr[] = '£' . number_format($summary['fundingApproved']) . ' funding approved';
                if ($summary['invoiced'] > 0) $nArr[] = '£' . number_format($summary['invoiced']) . ' invoiced';
            @endphp
            @if($nArr)
            <p class="text-sm text-gray-600 leading-relaxed mt-2">
                <i class="fa-solid fa-circle-info mr-1 opacity-50"></i>
                {{ ucfirst(implode(', ', $nArr)) }}.
                @if(!$allTime && $activeFunding)
                Active funding cycle: <strong>£{{ number_format($activeFunding->approved_amount) }}</strong> ({{ $activeFunding->funder_name }}).
                @endif
            </p>
            @endif
        </div>
    </div>
    @endif

    {{-- Upcoming appointments --}}
    @if($upcoming->isNotEmpty())
    <div class="rounded-xl border p-4 mb-5" style="border-color:#bfdbfe;background:#eff6ff;">
        <h3 class="text-sm font-semibold mb-3" style="color:#1e40af;">
            <i class="fa-solid fa-calendar-check mr-1"></i> Upcoming Appointments
        </h3>
        <div class="space-y-2">
            @foreach($upcoming as $appt)
            <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2 border border-blue-100">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($appt->scheduled_at)->format('D d M Y, H:i') }}</p>
                    <p class="text-xs text-gray-500">with {{ $appt->associate_name }} &middot; {{ $appt->location ?? 'No location' }}</p>
                </div>
                <span class="text-xs rounded-full px-2 py-0.5" style="background:#dbeafe;color:#1e40af;">Scheduled</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Full timeline --}}
    @if($logs->isEmpty())
    <div class="rounded-xl border border-gray-100 bg-white p-10 text-center text-gray-400">
        <i class="fa-solid fa-timeline text-3xl mb-3 block"></i>
        No activity logged for this patient yet.
    </div>
    @else
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
            Full Journey Timeline
            @if(!$allTime)
            <span class="ml-2 text-xs font-normal normal-case text-gray-400">Showing all events · range highlighted</span>
            @else
            <span class="ml-2 text-xs font-normal normal-case text-gray-400">{{ $logs->count() }} events — all time</span>
            @endif
        </h3>
    </div>

    @php
        $currentDate = null;
        $rangeFrom = \Carbon\Carbon::parse($dateFrom)->startOfDay();
        $rangeTo   = \Carbon\Carbon::parse($dateTo)->endOfDay();
    @endphp
    <div class="relative">
        <div class="absolute left-5 top-0 bottom-0 w-px" style="background:#e5e7eb;"></div>
        <div class="space-y-3">
            @foreach($logs as $log)
            @php
                $logDate    = $log->occurred_at->format('d M Y');
                $meta       = \App\Models\ActivityLog::actionMeta($log->action);
                $isEnquiry  = $log->subject_type === 'Enquiry';
                $inRange    = !$allTime && $log->occurred_at->between($rangeFrom, $rangeTo);
            @endphp
            @if($logDate !== $currentDate)
                @php $currentDate = $logDate; @endphp
                <div class="flex gap-4 items-center">
                    <div class="w-10 flex-shrink-0"></div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1">{{ $logDate }}</span>
                </div>
            @endif
            <div class="flex gap-4 items-start">
                <div class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                     style="background:#f8fafc;border:2px solid {{ $meta['color'] }};">
                    <i class="fa-solid {{ $meta['icon'] }} text-sm" style="color:{{ $meta['color'] }};"></i>
                </div>
                <div class="flex-1 rounded-xl border px-4 py-3 shadow-sm"
                     style="background:{{ $inRange ? '#fefce8' : ($isEnquiry ? '#fffbeb' : '#fff') }};border-color:{{ $inRange ? '#fde68a' : ($isEnquiry ? '#fef3c7' : '#f1f5f9') }};">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($inRange)
                            <span class="text-xs font-semibold rounded-full px-2 py-0.5" style="background:#fde68a;color:#92400e;">In Range</span>
                            @endif
                            @if($isEnquiry)
                            <span class="text-xs font-semibold rounded-full px-2 py-0.5" style="background:#fef3c7;color:#92400e;">Enquiry Phase</span>
                            @endif
                            <span class="text-sm font-medium text-gray-800">{{ $log->description }}</span>
                        </div>
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $log->occurred_at->format('H:i') }}</span>
                    </div>
                    <div class="flex flex-wrap gap-3 mt-1.5">
                        @if($log->user)
                        <span class="text-xs text-gray-500"><i class="fa-solid fa-user mr-1 opacity-50"></i>{{ $log->user->name }}</span>
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

    @elseif(request('patient_id'))
    <div class="rounded-xl border border-red-100 bg-red-50 p-6 text-center text-red-600">Patient not found.</div>
    @endif
</x-app-layout>
