<x-app-layout>
    <x-slot name="header">Understanding Each Page</x-slot>

    {{-- ══ INTRO ══ --}}
    <div class="mb-8 rounded-xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-white p-6">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-white" style="background:#6366f1;">
                <i class="fa-solid fa-table-columns text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Understanding Each Page</h2>
                <p class="mt-1 text-sm text-gray-600 leading-relaxed">What every section means and when to use it. Select a page below to explore its blocks, understand when to use each one, and see how it connects to the patient journey.</p>
            </div>
        </div>
    </div>

    {{-- ══ TABS ══ --}}
    <div class="mb-10" x-data="{ open: 'patient' }">

        {{-- ── Tab selectors ── --}}
        @php
        $tabs = [
            ['id'=>'patient',       'label'=>'Patient Page',           'icon'=>'fa-user-injured',       'color'=>'#6366f1', 'light'=>'#eef2ff', 'dark'=>'#4338ca'],
            ['id'=>'enquiry',       'label'=>'Enquiry Page',           'icon'=>'fa-envelope-open-text', 'color'=>'#3b82f6', 'light'=>'#eff6ff', 'dark'=>'#1d4ed8'],
            ['id'=>'appointments',  'label'=>'Appointments',           'icon'=>'fa-calendar-days',      'color'=>'#0f766e', 'light'=>'#f0fdfa', 'dark'=>'#065f46'],
            ['id'=>'finance',       'label'=>'Finance / VTA Invoices', 'icon'=>'fa-file-invoice-dollar','color'=>'#f59e0b', 'light'=>'#fffbeb', 'dark'=>'#b45309'],
            ['id'=>'associate-inv', 'label'=>'Associate Invoices',     'icon'=>'fa-file-invoice',       'color'=>'#8b5cf6', 'light'=>'#f5f3ff', 'dark'=>'#6d28d9'],
            ['id'=>'casenotes',     'label'=>'Case Notes',             'icon'=>'fa-file-lines',         'color'=>'#10b981', 'light'=>'#ecfdf5', 'dark'=>'#065f46'],
            ['id'=>'reports',       'label'=>'Reports',                'icon'=>'fa-chart-bar',          'color'=>'#dc2626', 'light'=>'#fef2f2', 'dark'=>'#991b1b'],
        ];
        @endphp
        <div class="flex flex-wrap gap-3 mb-6">
            @foreach($tabs as $tab)
            <button
                @click="open = '{{ $tab['id'] }}'"
                :style="open === '{{ $tab['id'] }}'
                    ? 'background:{{ $tab['color'] }}; border-color:{{ $tab['color'] }}; color:#fff; box-shadow:0 4px 14px {{ $tab['color'] }}55;'
                    : 'background:#fff; border-color:#e5e7eb; color:#374151;'"
                @mouseenter="if(open !== '{{ $tab['id'] }}') $event.currentTarget.style.cssText='background:{{ $tab['light'] }}; border-color:{{ $tab['color'] }}55; color:{{ $tab['dark'] }};'"
                @mouseleave="if(open !== '{{ $tab['id'] }}') $event.currentTarget.style.cssText='background:#fff; border-color:#e5e7eb; color:#374151;'"
                class="inline-flex items-center gap-2.5 rounded-2xl border px-6 py-3 text-sm font-semibold transition-all duration-200 cursor-pointer">
                <span class="flex h-7 w-7 items-center justify-center rounded-xl" :style="open === '{{ $tab['id'] }}' ? 'background:rgba(255,255,255,0.25)' : 'background:{{ $tab['light'] }}'">
                    <i class="fa-solid {{ $tab['icon'] }}" :style="open === '{{ $tab['id'] }}' ? 'color:#fff' : 'color:{{ $tab['color'] }}'"></i>
                </span>
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- PATIENT PAGE --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="open === 'patient'" x-transition.opacity class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" style="border-left: 4px solid #6366f1;">

            <div class="border-b border-gray-100 bg-gray-50/60 px-8 py-6">
                <p class="text-base text-gray-600">The <strong>Patient page</strong> is the central hub for every active case. It brings together clinical, financial, and communication data in one place. Here is what each section does:</p>
            </div>

            @php
            $patientBlocks = [
                ['num'=>'1','icon'=>'fa-id-card','title'=>'Patient Information','stage'=>'Stage 1–2','stage_color'=>'#3b82f6','what'=>'Core demographics — full name, date of birth, contact details, GP, referral date, location, and condition. This is filled automatically when an enquiry is converted to a patient.','when'=>'Review this at the start of every case. Edit if details change (new address, updated contact number).','link'=>''],
                ['num'=>'2','icon'=>'fa-arrow-right-arrow-left','title'=>'Current Status','stage'=>'All Stages','stage_color'=>'#6b7280','what'=>'Shows where the patient is in the clinical journey and lets you move them forward. The dropdown only offers the statuses that are valid next steps — the system prevents you from skipping stages.','when'=>'Update the status every time something meaningful happens: assessment booked, report sent, funding received, treatment started, discharged.','link'=>''],
                ['num'=>'3','icon'=>'fa-folder-open','title'=>'Documents','stage'=>'All Stages','stage_color'=>'#6b7280','what'=>'Stores all files related to this patient — referral letters, assessment reports, funding approval letters, consent forms, and discharge summaries. Each document type has configurable access permissions (Settings → Document Types) controlling who can view or download it.','when'=>'Upload as documents arrive. The funding approval letter must be uploaded before you can change status to Funding Approved.','link'=>''],
                ['num'=>'4','icon'=>'fa-comments','title'=>'Communications','stage'=>'All Stages','stage_color'=>'#6b7280','what'=>'A log of every phone call, email, and letter related to this patient — whether with the case manager, funder, or associate. Each entry can have a follow-up task with a due date. The Dashboard Daily Actions widget surfaces overdue follow-ups automatically.','when'=>'Log every contact immediately after it happens. This is your audit trail and replaces any paper or email chase records.','link'=>''],
                ['num'=>'5','icon'=>'fa-clipboard-medical','title'=>'Assessment','stage'=>'Stage 2','stage_color'=>'#6366f1','what'=>'Records the outcome of the vestibular assessment Samy conducts. Captures assessment date, key findings, report status (drafted/sent), and links to the assessment invoice. This section unlocks once the patient reaches Assessment Scheduled status.','when'=>'Fill in after the assessment appointment. A draft report must be logged here before status can move to Report Sent.','link'=>''],
                ['num'=>'6','icon'=>'fa-file-lines','title'=>'Case Notes','stage'=>'Stage 5','stage_color'=>'#22c55e','what'=>'All session notes submitted by the treating associate via their portal. Each note shows the session date, the associate\'s observations, and its review status. Samy signs off notes here — signed-off notes become visible to the case manager in their portal.','when'=>'Check regularly during treatment. The Dashboard shows a count of notes awaiting your sign-off.','link'=>''],
                ['num'=>'7','icon'=>'fa-timeline','title'=>'Patient Journey (Timeline)','stage'=>'All Stages','stage_color'=>'#6b7280','what'=>'A read-only, reverse-chronological feed of everything that has happened for this patient — status changes, communications, documents uploaded, appointments, case notes, and VTA invoices. Built automatically. Nothing is manually entered here.','when'=>'Use this to get a quick summary of where a case stands without reading through each individual section. Useful when picking up a case after a break.','link'=>''],
                ['num'=>'8','icon'=>'fa-building-user','title'=>'Case Manager','stage'=>'Stage 1','stage_color'=>'#3b82f6','what'=>'The named person at the referring company who is managing this patient on the funder\'s side. Linked when the enquiry is converted. They receive portal access to view treatment progress for their patients only.','when'=>'Update if the case manager changes company or a new contact takes over the file. Create their portal login here via Companies → Case Managers.','link'=>''],
                ['num'=>'9','icon'=>'fa-user-doctor','title'=>'Associates','stage'=>'Stage 5','stage_color'=>'#22c55e','what'=>'The clinicians allocated to treat this patient — physiotherapist, psychologist, occupational therapist etc. Each allocation has a role type and start date. Associates can only see patients they are allocated to in their portal.','when'=>'Add the associate before treatment starts. They will immediately see the patient in their Associate Portal.','link'=>''],
                ['num'=>'10','icon'=>'fa-note-sticky','title'=>'Notes','stage'=>'All Stages','stage_color'=>'#6b7280','what'=>'Free-text internal notes for Samy and staff only. Not visible to associates or case managers. Use for anything that doesn\'t fit a structured field — funder contact preferences, clinical reminders, flagged concerns.','when'=>'Add any time. Particularly useful for notes about a funder\'s payment behaviour or unusual case circumstances.','link'=>''],
                ['num'=>'11','icon'=>'fa-hand-holding-dollar','title'=>'Funding Overview','stage'=>'Stage 4–5','stage_color'=>'#f59e0b','what'=>'Shows all funding cycles for this patient — each cycle has an approved amount, the total invoiced so far, and the remaining balance. The portal calculates the balance automatically from paid VTA invoices. An amber warning appears when less than 20% remains; red when exhausted.','when'=>'Add a new funding cycle when a funder\'s approval letter arrives. Check the balance before raising each new VTA invoice to ensure you are not exceeding the approved amount.','link'=>''],
                ['num'=>'12','icon'=>'fa-people-group','title'=>'MDT Meetings','stage'=>'Stage 5','stage_color'=>'#22c55e','what'=>'Multi-Disciplinary Team meeting records linked to a patient. Captures meeting date, attendees, and key discussion notes. MDT entries sit alongside associate communications so the full clinical coordination history is in one place.','when'=>'Log an MDT meeting whenever a multi-team review takes place for this patient — whether face-to-face, by phone, or video. Each entry creates a permanent record of who was present and what was discussed.','link'=>''],
            ];
            $patientCardColors = [
                '1'=>['border'=>'#3b82f6','bg'=>'#eff6ff','badge_bg'=>'#dbeafe','badge_txt'=>'#1d4ed8'],
                '2'=>['border'=>'#6366f1','bg'=>'#eef2ff','badge_bg'=>'#e0e7ff','badge_txt'=>'#4338ca'],
                '3'=>['border'=>'#8b5cf6','bg'=>'#f5f3ff','badge_bg'=>'#ede9fe','badge_txt'=>'#6d28d9'],
                '4'=>['border'=>'#ec4899','bg'=>'#fdf2f8','badge_bg'=>'#fce7f3','badge_txt'=>'#be185d'],
                '5'=>['border'=>'#14b8a6','bg'=>'#f0fdfa','badge_bg'=>'#ccfbf1','badge_txt'=>'#0f766e'],
                '6'=>['border'=>'#22c55e','bg'=>'#f0fdf4','badge_bg'=>'#dcfce7','badge_txt'=>'#15803d'],
                '7'=>['border'=>'#f59e0b','bg'=>'#fffbeb','badge_bg'=>'#fef3c7','badge_txt'=>'#b45309'],
                '8'=>['border'=>'#f97316','bg'=>'#fff7ed','badge_bg'=>'#ffedd5','badge_txt'=>'#c2410c'],
                '9'=>['border'=>'#06b6d4','bg'=>'#ecfeff','badge_bg'=>'#cffafe','badge_txt'=>'#0e7490'],
                '10'=>['border'=>'#84cc16','bg'=>'#f7fee7','badge_bg'=>'#ecfccb','badge_txt'=>'#4d7c0f'],
                '11'=>['border'=>'#ef4444','bg'=>'#fef2f2','badge_bg'=>'#fee2e2','badge_txt'=>'#b91c1c'],
                '12'=>['border'=>'#0f766e','bg'=>'#f0fdfa','badge_bg'=>'#ccfbf1','badge_txt'=>'#065f46'],
            ];
            @endphp
            @foreach($patientBlocks as $block)
            @php $pc = $patientCardColors[$block['num']] ?? ['border'=>'#6b7280','bg'=>'#f9fafb','badge_bg'=>'#f3f4f6','badge_txt'=>'#374151']; @endphp
            <div class="rounded-xl shadow-sm overflow-hidden mb-4 last:mb-0" style="border:1px solid {{ $pc['border'] }}33; border-left:5px solid {{ $pc['border'] }}; background:{{ $pc['bg'] }};">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x p-6" style="border-color:{{ $pc['border'] }}22;">
                    <div class="pb-5 md:pb-0 md:pr-7">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white text-xs font-black shadow-sm" style="background:{{ $pc['border'] }};">{{ $block['num'] }}</div>
                            <span class="text-sm font-bold text-gray-800">{{ $block['title'] }}</span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap" style="background:{{ $pc['badge_bg'] }}; color:{{ $pc['badge_txt'] }};">{{ $block['stage'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['what'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:px-7">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:{{ $pc['border'] }};">When to use it</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['when'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:pl-7">
                        @if($block['num'] == '2')
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4"><p class="text-xs font-bold text-amber-700 mb-1.5 uppercase tracking-wide"><i class="fa-solid fa-shield-check mr-1"></i>Business rules</p><p class="text-sm text-amber-700">Status only moves <strong>forward</strong> through defined steps. The system blocks skipping stages. Funding Approved requires an uploaded document. Case Closed requires all invoices to be Paid.</p></div>
                        @elseif($block['num'] == '11')
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4"><p class="text-xs font-bold text-amber-700 mb-1.5 uppercase tracking-wide"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Watch for</p><p class="text-sm text-amber-700">Amber warning = less than 20% remaining. Red = fully exhausted. Both mean you should request further funding before raising more invoices.</p></div>
                        @elseif($block['num'] == '6')
                        <div class="rounded-xl bg-blue-50 border border-blue-200 p-4"><p class="text-xs font-bold text-blue-700 mb-1.5 uppercase tracking-wide"><i class="fa-solid fa-eye mr-1"></i>Visibility</p><p class="text-sm text-blue-700"><strong>Unsigned</strong> — Samy and staff only.<br><strong>Signed-off</strong> — also visible to the case manager portal.</p></div>
                        @elseif($block['num'] == '3')
                        <div class="rounded-xl bg-blue-50 border border-blue-200 p-4"><p class="text-xs font-bold text-blue-700 mb-1.5 uppercase tracking-wide"><i class="fa-solid fa-lock mr-1"></i>Permissions</p><p class="text-sm text-blue-700">Each document type has its own access rule. Go to <strong>Settings → Document Types</strong> to control which roles can view, download, or upload each type.</p></div>
                        @else
                        <div class="rounded-xl p-4" style="background:{{ $pc['badge_bg'] }}; border:1px solid {{ $pc['border'] }}33;">
                            <p class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:{{ $pc['border'] }};"><i class="fa-solid fa-circle-info mr-1"></i>Connects to</p>
                            <p class="text-sm leading-relaxed" style="color:{{ $pc['badge_txt'] }};">
                                @if($block['num']=='1') Filled automatically from the Enquiry when qualified. Edit via Patient → Edit.
                                @elseif($block['num']=='4') Follow-up tasks appear in Dashboard Daily Actions. Complete them here.
                                @elseif($block['num']=='5') Triggers creation of Assessment Invoice in Finance → VTA Invoices.
                                @elseif($block['num']=='7') Automatically populated — no manual entry needed. Full audit trail for compliance.
                                @elseif($block['num']=='8') Links to Companies module. Case manager portal login is managed there.
                                @elseif($block['num']=='9') Associate sees this patient in their portal once allocated. Remove to revoke access.
                                @elseif($block['num']=='10') Private to admin and staff. Associates and case managers cannot see this.
                                @elseif($block['num']=='12') Sits alongside Associate Communications on the patient page. MDT notes are visible to admin and staff — not shown in the associate or case manager portals.
                                @else —
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ENQUIRY PAGE --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="open === 'enquiry'" x-transition.opacity class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" style="border-left:4px solid #3b82f6;">
            <div class="border-b border-gray-100 bg-gray-50/60 px-8 py-6">
                <p class="text-base text-gray-600">The <strong>Enquiry page</strong> is where a new referral lives before it becomes a patient. Every enquiry must be <em>qualified</em> before a patient record is created. Here is what each section does:</p>
            </div>
            @php
            $enquiryBlocks = [
                ['num'=>'1','icon'=>'fa-file-alt','title'=>'Enquiry Information & Enquiry ID','what'=>'All the details from the initial referral — Enquiry ID (e.g. E004), enquirer name, referring company, case manager, referral source, patient condition, and current status. The Enquiry ID is assigned manually when you log the enquiry. Status moves from New → In Progress → Qualified → Converted (or Not Proceeding).','when'=>'Fill in as soon as a referral arrives. Always assign an Enquiry ID at this stage — it will automatically become the Patient ID when the enquiry is converted. Update the status as the case progresses.','tip_icon'=>'fa-arrow-right-arrow-left','tip_color'=>'blue','tip_title'=>'Enquiry ID → Patient ID','tip_body'=>'When an enquiry is converted to a patient record, the Enquiry ID (e.g. E004) automatically copies across as the Patient ID. You can edit the Patient ID on the patient record at any time after conversion.'],
                ['num'=>'2','icon'=>'fa-comments','title'=>'Communication Log','what'=>'A structured log of every interaction about this enquiry — phone calls with the case manager, emails sent, letters received. Each entry captures the date, type, direction (inbound/outbound), and a summary. This builds a full audit trail from first contact.','when'=>'Log every contact immediately. If a funder calls to ask about the referral, log it here. If you send an acknowledgement email, log it here. It should be the single source of truth for what has been said and when.','tip_icon'=>'fa-bell','tip_color'=>'amber','tip_title'=>'Follow-up tasks','tip_body'=>'When logging a communication, you can set a follow-up due date. Overdue follow-ups appear in the Dashboard Daily Actions — you should not need to remember them manually.'],
                ['num'=>'3','icon'=>'fa-check-square','title'=>'Follow-ups','what'=>'A consolidated list of all pending follow-up tasks created from the Communication Log. Shows what needs to be chased, when it is due, and who logged it. Tasks marked complete disappear from the list and from the Dashboard widget.','when'=>'Check this at the start of each working day alongside the Dashboard. Any overdue follow-ups flagged here should be your first priority.','tip_icon'=>'fa-circle-info','tip_color'=>'blue','tip_title'=>'Linked to Dashboard','tip_body'=>'The Dashboard Daily Actions widget shows overdue follow-ups across all enquiries and patients in one place. Completing a follow-up here removes it from the Dashboard count.'],
                ['num'=>'4','icon'=>'fa-folder','title'=>'Documents','what'=>'Attach files received at the enquiry stage — the original referral letter, a medical summary from the GP, any early correspondence from the solicitor. These documents transfer to the patient record when the enquiry is converted.','when'=>'Upload as soon as documents arrive. Do not wait until conversion — having everything attached from the start means nothing is lost if the case takes weeks to progress.','tip_icon'=>'fa-arrow-right','tip_color'=>'green','tip_title'=>'Carries over on conversion','tip_body'=>'All documents attached to an enquiry are automatically available on the patient record after conversion. You do not need to upload them again.'],
            ];
            $enqCardColors = [
                ['border'=>'#3b82f6','bg'=>'#eff6ff','badge_bg'=>'#dbeafe','badge_txt'=>'#1d4ed8'],
                ['border'=>'#6366f1','bg'=>'#eef2ff','badge_bg'=>'#e0e7ff','badge_txt'=>'#4338ca'],
                ['border'=>'#14b8a6','bg'=>'#f0fdfa','badge_bg'=>'#ccfbf1','badge_txt'=>'#0f766e'],
                ['border'=>'#ec4899','bg'=>'#fdf2f8','badge_bg'=>'#fce7f3','badge_txt'=>'#be185d'],
            ];
            @endphp
            @foreach($enquiryBlocks as $block)
            @php
            $ec = $enqCardColors[$loop->index % count($enqCardColors)];
            $tc = $block['tip_color'];
            $bgMap = ['blue'=>['bg'=>'#eff6ff','border'=>'#bfdbfe','txt'=>'#1d4ed8'],'amber'=>['bg'=>'#fffbeb','border'=>'#fde68a','txt'=>'#b45309'],'green'=>['bg'=>'#f0fdf4','border'=>'#bbf7d0','txt'=>'#15803d']];
            $tipStyle = $bgMap[$tc] ?? ['bg'=>'#f9fafb','border'=>'#e5e7eb','txt'=>'#374151'];
            @endphp
            <div class="rounded-xl shadow-sm overflow-hidden mb-4 last:mb-0" style="border:1px solid {{ $ec['border'] }}33; border-left:5px solid {{ $ec['border'] }}; background:{{ $ec['bg'] }};">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x p-6" style="border-color:{{ $ec['border'] }}22;">
                    <div class="pb-5 md:pb-0 md:pr-7">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white text-xs font-black shadow-sm" style="background:{{ $ec['border'] }};">{{ $block['num'] }}</div>
                            <span class="text-sm font-bold text-gray-800">{{ $block['title'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['what'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:px-7">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:{{ $ec['border'] }};">When to use it</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['when'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:pl-7">
                        <div class="rounded-xl p-4" style="background:{{ $tipStyle['bg'] }}; border:1px solid {{ $tipStyle['border'] }};">
                            <p class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:{{ $tipStyle['txt'] }};"><i class="fa-solid {{ $block['tip_icon'] }} mr-1"></i>{{ $block['tip_title'] }}</p>
                            <p class="text-sm leading-relaxed" style="color:{{ $tipStyle['txt'] }};">{{ $block['tip_body'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- APPOINTMENTS --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="open === 'appointments'" x-transition.opacity class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" style="border-left:4px solid #0f766e;">
            <div class="border-b border-gray-100 bg-gray-50/60 px-8 py-6">
                <p class="text-base text-gray-600">The <strong>Appointments</strong> section is how VTA schedules and tracks every session — assessments, treatment appointments, and reviews. It has both a <em>list view per patient</em> and a <em>full calendar view</em> across all patients.</p>
            </div>
            @php
            $apptBlocks = [
                ['num'=>'1','icon'=>'fa-calendar-days','title'=>'The Appointments Calendar','what'=>'A full monthly/weekly calendar showing every appointment across all patients and associates. Colour-coded by status — Scheduled (blue), Completed (green), DNA (amber), Cancelled (grey). You can click any appointment to view its details.','when'=>'Use this as your daily planning tool. Open it at the start of each day to see what is booked. Use the week view to spot gaps or clashes across multiple associates.','tip_icon'=>'fa-filter','tip_color'=>'teal','tip_title'=>'Filter by associate','tip_body'=>'You can filter the calendar to show only one associate\'s appointments — useful when coordinating a specific therapist\'s diary.'],
                ['num'=>'2','icon'=>'fa-calendar-plus','title'=>'Booking an Appointment','what'=>'Appointments are created from the patient\'s record (Patient → Appointments section → Add) or directly from the calendar. Each appointment captures: patient, associate, date, time, type (Assessment / Treatment / Review / Other), location, and any notes.','when'=>'Book as soon as a session is agreed with the associate. Do not leave it unbooked — the calendar is the source of truth for what is scheduled. If an appointment is changed, update it here immediately.','tip_icon'=>'fa-triangle-exclamation','tip_color'=>'amber','tip_title'=>'Session count tracking','tip_body'=>'The patient\'s associate allocation tracks sessions approved vs sessions used. Completed appointments increment the sessions used count — so keeping statuses accurate is important for knowing when a funding top-up is needed.'],
                ['num'=>'3','icon'=>'fa-list-check','title'=>'Appointment Outcomes','what'=>'After each appointment, update the status to reflect what happened. Completed — session delivered as planned. DNA (Did Not Attend) — patient did not show. Cancelled — session was cancelled in advance. Each outcome is recorded in the patient timeline automatically.','when'=>'Update the status the same day as the appointment. DNA and Cancelled appointments should be noted in the communications log as well — so the case manager and funder can see what happened if queried.','tip_icon'=>'fa-circle-info','tip_color'=>'teal','tip_title'=>'DNA and Cancelled affect billing','tip_body'=>'Check with Samy\'s billing policy on whether DNA or Cancelled sessions are billable. If they are, raise a VTA Invoice as normal. If not, they still appear on the timeline for reference.'],
            ];
            $apptCardColors = [
                ['border'=>'#0f766e','bg'=>'#f0fdfa','badge_bg'=>'#ccfbf1','badge_txt'=>'#065f46'],
                ['border'=>'#0891b2','bg'=>'#ecfeff','badge_bg'=>'#cffafe','badge_txt'=>'#0e7490'],
                ['border'=>'#0284c7','bg'=>'#f0f9ff','badge_bg'=>'#dbeafe','badge_txt'=>'#1d4ed8'],
            ];
            $apptTipMap = ['teal'=>['bg'=>'#f0fdfa','border'=>'#99f6e4','txt'=>'#065f46'],'amber'=>['bg'=>'#fffbeb','border'=>'#fde68a','txt'=>'#b45309']];
            @endphp
            @foreach($apptBlocks as $block)
            @php
            $apc = $apptCardColors[$loop->index % count($apptCardColors)];
            $apptTipStyle = $apptTipMap[$block['tip_color']] ?? ['bg'=>'#f0fdfa','border'=>'#99f6e4','txt'=>'#065f46'];
            @endphp
            <div class="rounded-xl shadow-sm overflow-hidden mb-4 last:mb-0" style="border:1px solid {{ $apc['border'] }}33; border-left:5px solid {{ $apc['border'] }}; background:{{ $apc['bg'] }};">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x p-6" style="border-color:{{ $apc['border'] }}22;">
                    <div class="pb-5 md:pb-0 md:pr-7">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white text-xs font-black shadow-sm" style="background:{{ $apc['border'] }};">{{ $block['num'] }}</div>
                            <span class="text-sm font-bold text-gray-800">{{ $block['title'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['what'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:px-7">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:{{ $apc['border'] }};">When to use it</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['when'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:pl-7">
                        <div class="rounded-xl p-4" style="background:{{ $apptTipStyle['bg'] }}; border:1px solid {{ $apptTipStyle['border'] }};">
                            <p class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:{{ $apptTipStyle['txt'] }};"><i class="fa-solid {{ $block['tip_icon'] }} mr-1"></i>{{ $block['tip_title'] }}</p>
                            <p class="text-sm leading-relaxed" style="color:{{ $apptTipStyle['txt'] }};">{{ $block['tip_body'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- FINANCE / VTA INVOICES --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="open === 'finance'" x-transition.opacity class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" style="border-left:4px solid #f59e0b;">
            <div class="border-b border-gray-100 bg-gray-50/60 px-8 py-6">
                <p class="text-base text-gray-600">The <strong>Finance section</strong> covers money VTA sends out (VTA Invoices to funders) and money VTA receives from associates (Associate Invoices). Here is how each part works:</p>
            </div>
            @php
            $financeBlocks = [
                ['num'=>'1','icon'=>'fa-file-invoice-dollar','title'=>'VTA Invoices (Outgoing)','what'=>'Invoices that VTA raises and sends to funders — insurers, solicitors, or employers — billing for treatment sessions delivered. Each invoice is linked to a patient and a funding cycle so the balance automatically updates.','when'=>'Create a VTA Invoice after each batch of treatment sessions. Also create one for the assessment report fee. Invoice status: Draft → Sent → Paid. Mark as Paid when the funder\'s payment arrives.','tip_icon'=>'fa-hashtag','tip_color'=>'amber','tip_title'=>'Auto-numbered','tip_body'=>'Invoice numbers are generated automatically in sequence (VTA-2026-001, VTA-2026-002…). You cannot manually set the number.'],
                ['num'=>'2','icon'=>'fa-file-invoice','title'=>'VTA Invoice Status Flow','what'=>'Draft — created but not yet sent. Sent — the invoice PDF has been sent to the funder (upload the document to record this). Paid — payment received. Overdue — the due date has passed and the invoice is still unpaid.','when'=>'Move to Sent when you email or post the invoice to the funder. Move to Paid as soon as the bank payment clears. Never leave invoices in Draft if they have been sent — the outstanding balance report depends on accurate statuses.','tip_icon'=>'fa-ban','tip_color'=>'red','tip_title'=>'Blocks case closure','tip_body'=>'Any VTA invoice that is not Paid will block the patient status from moving to Case Closed. All invoices must be settled before you can close a case.'],
                ['num'=>'3','icon'=>'fa-gauge-high','title'=>'Finance Dashboard Summary','what'=>'At the top of the VTA Invoices list: Invoiced This Month, Paid This Month, Total Outstanding, and Overdue Count. These update live as you change invoice statuses.','when'=>'Check at the start of each week. If Outstanding is high, review which invoices are sitting at Sent and chase the funders.','tip_icon'=>'fa-chart-bar','tip_color'=>'amber','tip_title'=>'Detailed reports','tip_body'=>'Finance → Reports shows revenue by company (which funder sends the most cases), aged debt (0–30, 31–60, 60+ days overdue), and associate payment summary.'],
            ];
            $finCardColors = [
                ['border'=>'#f59e0b','bg'=>'#fffbeb','badge_bg'=>'#fef3c7','badge_txt'=>'#b45309'],
                ['border'=>'#f97316','bg'=>'#fff7ed','badge_bg'=>'#ffedd5','badge_txt'=>'#c2410c'],
                ['border'=>'#06b6d4','bg'=>'#ecfeff','badge_bg'=>'#cffafe','badge_txt'=>'#0e7490'],
            ];
            @endphp
            @foreach($financeBlocks as $block)
            @php
            $fc = $finCardColors[$loop->index % count($finCardColors)];
            $tc = $block['tip_color'];
            $finTipMap = ['amber'=>['bg'=>'#fffbeb','border'=>'#fde68a','txt'=>'#b45309'],'red'=>['bg'=>'#fef2f2','border'=>'#fecaca','txt'=>'#b91c1c']];
            $finTipStyle = $finTipMap[$tc] ?? ['bg'=>'#f9fafb','border'=>'#e5e7eb','txt'=>'#374151'];
            @endphp
            <div class="rounded-xl shadow-sm overflow-hidden mb-4 last:mb-0" style="border:1px solid {{ $fc['border'] }}33; border-left:5px solid {{ $fc['border'] }}; background:{{ $fc['bg'] }};">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x p-6" style="border-color:{{ $fc['border'] }}22;">
                    <div class="pb-5 md:pb-0 md:pr-7">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white text-xs font-black shadow-sm" style="background:{{ $fc['border'] }};">{{ $block['num'] }}</div>
                            <span class="text-sm font-bold text-gray-800">{{ $block['title'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['what'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:px-7">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:{{ $fc['border'] }};">When to use it</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['when'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:pl-7">
                        <div class="rounded-xl p-4" style="background:{{ $finTipStyle['bg'] }}; border:1px solid {{ $finTipStyle['border'] }};">
                            <p class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:{{ $finTipStyle['txt'] }};"><i class="fa-solid {{ $block['tip_icon'] }} mr-1"></i>{{ $block['tip_title'] }}</p>
                            <p class="text-sm leading-relaxed" style="color:{{ $finTipStyle['txt'] }};">{{ $block['tip_body'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ASSOCIATE INVOICES --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="open === 'associate-inv'" x-transition.opacity class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" style="border-left:4px solid #8b5cf6;">
            <div class="border-b border-gray-100 bg-gray-50/60 px-8 py-6">
                <p class="text-base text-gray-600"><strong>Associate Invoices</strong> are bills that associates send to VTA for the sessions they have delivered. VTA logs them here, verifies them against the case, and marks them as paid when the associate is remunerated.</p>
            </div>
            @php
            $assocBlocks = [
                ['num'=>'1','icon'=>'fa-file-invoice','title'=>'What an Associate Invoice is','what'=>'When an associate completes sessions with a patient, they invoice VTA for their work. This is separate from the VTA Invoice you send to the funder. The associate invoice captures: sessions completed, travel miles, session rate, travel rate, and total payable.','when'=>'Log when you receive an invoice from the associate — either by post, email, or when they notify you. Link it to the correct patient and funding cycle.','tip_icon'=>'fa-arrows-left-right','tip_color'=>'violet','tip_title'=>'Two separate money flows','tip_body'=>'VTA Invoices = money coming IN from funders. Associate Invoices = money going OUT to associates. Both are linked to the same patient and funding cycle.'],
                ['num'=>'2','icon'=>'fa-list-check','title'=>'Associate Invoice Status Flow','what'=>'Received — the invoice has arrived and been logged. Verified — you have checked the sessions against the case notes and the amounts are correct. Paid — the associate has been paid. Queried — there is a discrepancy that needs to be resolved before payment.','when'=>'Verify before paying — cross-check the sessions claimed against the case notes on the patient record. Only mark Paid once the bank transfer is done.','tip_icon'=>'fa-magnifying-glass','tip_color'=>'violet','tip_title'=>'Cross-check against case notes','tip_body'=>'Before verifying, check Finance → Associate Invoices then open the patient and count the signed-off case notes for that associate. Sessions invoiced should match sessions documented.'],
                ['num'=>'3','icon'=>'fa-chart-pie','title'=>'Associate Payment Summary (Reports)','what'=>'Finance → Reports shows a breakdown per associate: total invoiced to VTA, total paid, and any overdue amounts. This tells you at a glance who is owed money and whether any associate invoices are outstanding.','when'=>'Review monthly when processing associate payments. If an associate is overdue, check whether their invoices are at Received (not yet verified) or Verified (awaiting payment run).','tip_icon'=>'fa-circle-info','tip_color'=>'violet','tip_title'=>'No automated payment','tip_body'=>'The portal tracks invoice status but does not initiate bank transfers. You process the actual payment through your bank as normal — then update the invoice status to Paid here.'],
            ];
            $assocCardColors = [
                ['border'=>'#8b5cf6','bg'=>'#f5f3ff','badge_bg'=>'#ede9fe','badge_txt'=>'#6d28d9'],
                ['border'=>'#a855f7','bg'=>'#faf5ff','badge_bg'=>'#f3e8ff','badge_txt'=>'#7e22ce'],
                ['border'=>'#6366f1','bg'=>'#eef2ff','badge_bg'=>'#e0e7ff','badge_txt'=>'#4338ca'],
            ];
            @endphp
            @foreach($assocBlocks as $block)
            @php
            $ac = $assocCardColors[$loop->index % count($assocCardColors)];
            @endphp
            <div class="rounded-xl shadow-sm overflow-hidden mb-4 last:mb-0" style="border:1px solid {{ $ac['border'] }}33; border-left:5px solid {{ $ac['border'] }}; background:{{ $ac['bg'] }};">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x p-6" style="border-color:{{ $ac['border'] }}22;">
                    <div class="pb-5 md:pb-0 md:pr-7">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white text-xs font-black shadow-sm" style="background:{{ $ac['border'] }};">{{ $block['num'] }}</div>
                            <span class="text-sm font-bold text-gray-800">{{ $block['title'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['what'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:px-7">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:{{ $ac['border'] }};">When to use it</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['when'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:pl-7">
                        <div class="rounded-xl p-4" style="background:{{ $ac['badge_bg'] }}; border:1px solid {{ $ac['border'] }}44;">
                            <p class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:{{ $ac['badge_txt'] }};"><i class="fa-solid {{ $block['tip_icon'] }} mr-1"></i>{{ $block['tip_title'] }}</p>
                            <p class="text-sm leading-relaxed" style="color:{{ $ac['badge_txt'] }};">{{ $block['tip_body'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- CASE NOTES --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="open === 'casenotes'" x-transition.opacity class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" style="border-left:4px solid #22c55e;">
            <div class="border-b border-gray-100 bg-gray-50/60 px-8 py-6">
                <p class="text-base text-gray-600"><strong>Case Notes</strong> are the clinical record of each session an associate delivers. Associates upload them through their portal; Samy reviews and signs them off. Once signed off, they are visible to the case manager.</p>
            </div>
            @php
            $noteBlocks = [
                ['num'=>'1','icon'=>'fa-file-pen','title'=>'Who writes case notes?','what'=>'Associates write and upload case notes from their portal after each session. They cannot be edited by admin or staff — the clinical record belongs to the associate. Samy can add a review comment but not alter the note itself.','when'=>'After each treatment session, the associate should upload their note within 24–48 hours. If a note is missing for a session that was billed, query it before paying the associate invoice.','tip_icon'=>'fa-user-lock','tip_color'=>'green','tip_title'=>'Associate-authored','tip_body'=>'Case notes are the clinical responsibility of the associate who wrote them. Samy\'s sign-off confirms he has reviewed them as Clinical Head — it does not transfer clinical responsibility.'],
                ['num'=>'2','icon'=>'fa-clipboard-check','title'=>'Sign-off and Review','what'=>'Once an associate submits a note, it appears in the Case Notes list with status "Pending Review". Samy reviews and clicks Sign Off. Signed-off notes become visible to the case manager in their portal. If a note needs changes, Samy contacts the associate directly (the portal will have a feedback feature in a future release).','when'=>'Review new case notes regularly — the Dashboard shows how many are awaiting sign-off. Aim to sign off within a few days of receipt, as case managers may be waiting to view progress.','tip_icon'=>'fa-eye','tip_color'=>'green','tip_title'=>'Case manager visibility','tip_body'=>'Case managers can only see notes that have been signed off by Samy. Unsigned notes are private to Samy and staff. This ensures clinical review happens before external visibility.'],
                ['num'=>'3','icon'=>'fa-flag','title'=>'Flagged for Clinical Head Review','what'=>'Associates can flag a note as needing Samy\'s specific clinical attention — for example, if the patient\'s condition has changed significantly or a clinical decision is needed. These appear highlighted in the Case Notes list.','when'=>'If an associate flags a note, treat it as urgent. It means the associate has encountered something they need your clinical judgement on before proceeding with the next session.','tip_icon'=>'fa-bell','tip_color'=>'green','tip_title'=>'Dashboard alert','tip_body'=>'Flagged notes appear as a separate counter in the Dashboard Clinical Review widget. They will not disappear until you sign them off.'],
            ];
            $noteCardColors = [
                ['border'=>'#22c55e','bg'=>'#f0fdf4','badge_bg'=>'#dcfce7','badge_txt'=>'#15803d'],
                ['border'=>'#10b981','bg'=>'#ecfdf5','badge_bg'=>'#d1fae5','badge_txt'=>'#065f46'],
                ['border'=>'#14b8a6','bg'=>'#f0fdfa','badge_bg'=>'#ccfbf1','badge_txt'=>'#0f766e'],
            ];
            @endphp
            @foreach($noteBlocks as $block)
            @php $nc = $noteCardColors[$loop->index % count($noteCardColors)]; @endphp
            <div class="rounded-xl shadow-sm overflow-hidden mb-4 last:mb-0" style="border:1px solid {{ $nc['border'] }}33; border-left:5px solid {{ $nc['border'] }}; background:{{ $nc['bg'] }};">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x p-6" style="border-color:{{ $nc['border'] }}22;">
                    <div class="pb-5 md:pb-0 md:pr-7">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white text-xs font-black shadow-sm" style="background:{{ $nc['border'] }};">{{ $block['num'] }}</div>
                            <span class="text-sm font-bold text-gray-800">{{ $block['title'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['what'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:px-7">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:{{ $nc['border'] }};">When to use it</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['when'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:pl-7">
                        <div class="rounded-xl p-4" style="background:{{ $nc['badge_bg'] }}; border:1px solid {{ $nc['border'] }}44;">
                            <p class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:{{ $nc['badge_txt'] }};"><i class="fa-solid {{ $block['tip_icon'] }} mr-1"></i>{{ $block['tip_title'] }}</p>
                            <p class="text-sm leading-relaxed" style="color:{{ $nc['badge_txt'] }};">{{ $block['tip_body'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- REPORTS --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="open === 'reports'" x-transition.opacity class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" style="border-left:4px solid #7c3aed;">
            <div class="border-b border-gray-100 bg-gray-50/60 px-8 py-6">
                <p class="text-base text-gray-600">The <strong>Reports</strong> section gives Samy and Sheeba a management-level view of the portal data — five pre-built reports covering finances, patient activity, and associate workload. These are read-only summaries drawn from live data.</p>
            </div>
            @php
            $reportBlocks = [
                ['num'=>'1','icon'=>'fa-scale-balanced','title'=>'Funding Balance Report','what'=>'Shows the remaining funding balance for each active patient — how much has been approved by the funder vs how much has been invoiced so far. Flags patients where the balance is low or exhausted.','when'=>'Check this weekly, or before any new session block is booked. If a patient\'s balance is nearly zero, a funding top-up request must go to the case manager before the next sessions can proceed.','tip_icon'=>'fa-triangle-exclamation','tip_color'=>'purple','tip_title'=>'Act before zero','tip_body'=>'Do not wait for the balance to hit zero before contacting the case manager. A top-up request takes time — start the conversation when the balance falls below two sessions\' worth of funding.'],
                ['num'=>'2','icon'=>'fa-receipt','title'=>'Financial Summary Report','what'=>'A high-level financial overview — total VTA invoices raised, total associate invoices received, and the net position for a selected date range. Useful for monthly finance reviews and VAT reconciliation.','when'=>'Run this at month-end and send to the accountant. Also run it before any finance meeting with the case management companies so you have the current invoiced position ready.','tip_icon'=>'fa-calendar-check','tip_color'=>'purple','tip_title'=>'Date range filter','tip_body'=>'Always set the date range explicitly before exporting. The default shows all time — a large range can produce a misleadingly large total that does not match the period you are reviewing.'],
                ['num'=>'3','icon'=>'fa-users-line','title'=>'Patients by Status Report','what'=>'Lists all patients grouped by their current status: Active, Awaiting Funding, Treatment Complete, Closed, etc. Shows count per status and lists individual patients within each group.','when'=>'Use this for pipeline reviews — how many patients are active this month, how many are stalled waiting for funding, how many have completed treatment. It is also the quickest way to spot patients who have been sitting in "Awaiting Funding" for too long.','tip_icon'=>'fa-circle-info','tip_color'=>'purple','tip_title'=>'Status is set manually','tip_body'=>'Patient status does not change automatically — it must be updated on the patient record. If the report shows stale statuses, it is because the admin has not updated the patient records. Make it a habit to review and update statuses weekly.'],
                ['num'=>'4','icon'=>'fa-user-tie','title'=>'Associate Activity Report','what'=>'Shows each associate\'s activity for a period: number of appointments completed, total sessions delivered, and total associate invoice value. Helps Samy see who is active and identify associates with no recent activity.','when'=>'Review monthly or quarterly. If an associate shows zero activity for more than four weeks, check whether they have been assigned patients — there may be a mismatch between the associates who are on the system and those who are actually working cases.','tip_icon'=>'fa-chart-bar','tip_color'=>'purple','tip_title'=>'Invoicing cross-check','tip_body'=>'Cross-reference this report against associate invoices received. If an associate shows 10 completed sessions but only one invoice has been received, follow up — the associate may be behind on their invoicing.'],
                ['num'=>'5','icon'=>'fa-table-list','title'=>'Master Log Report','what'=>'A full export of all portal activity — every patient, every appointment, every invoice, every status change — in a single downloadable table. This is the raw data extract for audit purposes or bespoke analysis in Excel.','when'=>'Use this when you need data that the other four reports do not surface, or when an external auditor or funder asks for a comprehensive activity log. It is not meant for regular daily use — it is the escape hatch when a custom view is needed.','tip_icon'=>'fa-file-export','tip_color'=>'purple','tip_title'=>'Export to Excel','tip_body'=>'The Master Log can be downloaded as a CSV. Open it in Excel to apply your own filters, groupings, or pivot tables. This is the recommended path for any ad-hoc reporting that falls outside the five built-in report types.'],
            ];
            $repCardColors = [
                ['border'=>'#7c3aed','bg'=>'#faf5ff','badge_bg'=>'#ede9fe','badge_txt'=>'#5b21b6'],
                ['border'=>'#6d28d9','bg'=>'#f5f3ff','badge_bg'=>'#ddd6fe','badge_txt'=>'#4c1d95'],
                ['border'=>'#8b5cf6','bg'=>'#f8f5ff','badge_bg'=>'#e9d5ff','badge_txt'=>'#6d28d9'],
                ['border'=>'#a78bfa','bg'=>'#fdfaff','badge_bg'=>'#ede9fe','badge_txt'=>'#5b21b6'],
                ['border'=>'#7c3aed','bg'=>'#faf5ff','badge_bg'=>'#ede9fe','badge_txt'=>'#5b21b6'],
            ];
            @endphp
            @foreach($reportBlocks as $block)
            @php $rc = $repCardColors[$loop->index % count($repCardColors)]; @endphp
            <div class="rounded-xl shadow-sm overflow-hidden mb-4 last:mb-0" style="border:1px solid {{ $rc['border'] }}33; border-left:5px solid {{ $rc['border'] }}; background:{{ $rc['bg'] }};">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x p-6" style="border-color:{{ $rc['border'] }}22;">
                    <div class="pb-5 md:pb-0 md:pr-7">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white text-xs font-black shadow-sm" style="background:{{ $rc['border'] }};">{{ $block['num'] }}</div>
                            <span class="text-sm font-bold text-gray-800">{{ $block['title'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['what'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:px-7">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:{{ $rc['border'] }};">When to use it</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $block['when'] }}</p>
                    </div>
                    <div class="pt-5 md:pt-0 md:pl-7">
                        <div class="rounded-xl p-4" style="background:{{ $rc['badge_bg'] }}; border:1px solid {{ $rc['border'] }}44;">
                            <p class="text-xs font-bold uppercase tracking-wide mb-1.5" style="color:{{ $rc['badge_txt'] }};"><i class="fa-solid {{ $block['tip_icon'] }} mr-1"></i>{{ $block['tip_title'] }}</p>
                            <p class="text-sm leading-relaxed" style="color:{{ $rc['badge_txt'] }};">{{ $block['tip_body'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    {{-- ══ BACK LINK ══ --}}
    <div class="mt-4">
        <a href="{{ route('how-it-works') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#0092b4] transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Patient Lifecycle
        </a>
    </div>

</x-app-layout>
