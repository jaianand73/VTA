<x-app-layout>
    @php
        $tested  = $stats['total'];
        $total   = 50;
        $pctPass = $total > 0 ? round(($stats['pass']        / $total) * 100) : 0;
        $pctFail = $total > 0 ? round(($stats['fail']        / $total) * 100) : 0;
        $pctSug  = $total > 0 ? round(($stats['improvement'] / $total) * 100) : 0;
        $pctDone = $pctPass + $pctFail + $pctSug;
        $remaining = $total - $tested;

        $message = match(true) {
            $tested === 0                     => 'Start testing — work through each step and record your result.',
            $tested < 10                      => 'Good start! Keep going — ' . $remaining . ' steps remaining.',
            $tested < 20                      => 'Making progress! ' . $remaining . ' steps to go.',
            $tested < 35                      => 'Almost there — just ' . $remaining . ' more steps!',
            $tested < $total                  => 'Nearly done — ' . $remaining . ' step' . ($remaining === 1 ? '' : 's') . ' left!',
            $stats['fail'] === 0              => 'All steps tested — no failures. Excellent work!',
            default                           => 'All steps tested. ' . $stats['fail'] . ' item' . ($stats['fail'] === 1 ? '' : 's') . ' to review.',
        };
    @endphp

    <x-slot name="topbar">
        <div style="display:flex; align-items:center; gap:14px; width:100%;">

            {{-- Label --}}
            <div style="display:flex; align-items:center; gap:7px; flex-shrink:0;">
                <div style="width:26px; height:26px; border-radius:50%; background:#0092b4; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-flask-vial" style="color:#fff; font-size:11px;"></i>
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#111827; line-height:1.1;">UAT Testing</div>
                    <div style="font-size:11px; color:#9ca3af; line-height:1;">{{ $stats['total'] }}/50 steps</div>
                </div>
            </div>

            {{-- Progress bar + segments --}}
            <div style="flex:1; min-width:0;">
                <div style="height:6px; background:#e5e7eb; border-radius:999px; overflow:hidden; display:flex;">
                    @if($pctPass > 0)<div style="width:{{ $pctPass }}%; background:#16a34a;"></div>@endif
                    @if($pctSug  > 0)<div style="width:{{ $pctSug  }}%; background:#d97706;"></div>@endif
                    @if($pctFail > 0)<div style="width:{{ $pctFail }}%; background:#dc2626;"></div>@endif
                </div>
                <div style="font-size:11px; color:#0092b4; margin-top:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $message }}
                </div>
            </div>

            {{-- Stat chips --}}
            <div style="display:flex; gap:6px; flex-shrink:0;">
                @if($stats['pass'] > 0)
                <span style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:999px; padding:2px 9px; font-size:11px; font-weight:600; color:#15803d;">
                    <i class="fa-solid fa-circle-check" style="font-size:9px;"></i> {{ $stats['pass'] }}
                </span>
                @endif
                @if($stats['fail'] > 0)
                <span style="background:#fef2f2; border:1px solid #fecaca; border-radius:999px; padding:2px 9px; font-size:11px; font-weight:600; color:#b91c1c;">
                    <i class="fa-solid fa-circle-xmark" style="font-size:9px;"></i> {{ $stats['fail'] }}
                </span>
                @endif
                @if($stats['improvement'] > 0)
                <span style="background:#fffbeb; border:1px solid #fde68a; border-radius:999px; padding:2px 9px; font-size:11px; font-weight:600; color:#92400e;">
                    <i class="fa-solid fa-lightbulb" style="font-size:9px;"></i> {{ $stats['improvement'] }}
                </span>
                @endif
            </div>

        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">


            {{-- ══ LOGIN CREDENTIALS ══ --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <div class="w-7 h-7 rounded-full bg-gray-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-key text-[10px]"></i>
                    </div>
                    <span class="font-semibold text-gray-800 text-sm">Login Credentials for UAT Testing</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                <th class="px-5 py-3 border-b border-gray-200">Name</th>
                                <th class="px-5 py-3 border-b border-gray-200">Role</th>
                                <th class="px-5 py-3 border-b border-gray-200">Email</th>
                                <th class="px-5 py-3 border-b border-gray-200">Password</th>
                                <th class="px-5 py-3 border-b border-gray-200">Access Level</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-5 py-3 font-semibold text-gray-800">Samy</td>
                                <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5">Admin</span></td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">samy@vta.co.uk</td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">ChangeMe2026!</td>
                                <td class="px-5 py-3 text-gray-600 text-xs">Full system — all modules, finance, settings, qualify gate</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-3 font-semibold text-gray-800">Sheeba</td>
                                <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-purple-100 text-purple-700 text-xs font-semibold px-2.5 py-0.5">Staff</span></td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">sheeba@vta.co.uk</td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">ChangeMe2026!</td>
                                <td class="px-5 py-3 text-gray-600 text-xs">Enquiries, patients, communications — no finance, no settings, cannot qualify</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-3 font-semibold text-gray-800">Kate</td>
                                <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-0.5">Associate</span></td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">kate@vta.co.uk</td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">ChangeMe2026!</td>
                                <td class="px-5 py-3 text-gray-600 text-xs">Associate portal only — own patients, case notes, calendar</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-3 font-semibold text-gray-800">Sarah</td>
                                <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 text-xs font-semibold px-2.5 py-0.5">Case Manager</span></td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">sarah@company.co.uk</td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">ChangeMe2026!</td>
                                <td class="px-5 py-3 text-gray-600 text-xs">Case manager portal — read-only view of own company's patients</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-3 font-semibold text-gray-800">Michael</td>
                                <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 text-xs font-semibold px-2.5 py-0.5">Case Manager</span></td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">michael@company.co.uk</td>
                                <td class="px-5 py-3 font-mono text-xs bg-gray-50 text-gray-700">ChangeMe2026!</td>
                                <td class="px-5 py-3 text-gray-600 text-xs">Case manager portal — read-only, different company from Sarah</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ══ HOW TO USE THIS GUIDE ══ --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <div class="w-7 h-7 rounded-full bg-gray-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">i</div>
                    <span class="font-semibold text-gray-800 text-sm">How to Use This Guide</span>
                </div>
                <div class="px-5 py-4 space-y-4">
                    {{-- 2×2 legend grid --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div style="display:flex; align-items:flex-start; gap:10px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px;">
                            <span style="width:12px; height:12px; border-radius:50%; background:#16a34a; flex-shrink:0; margin-top:3px;"></span>
                            <span style="font-size:13px; color:#374151;"><strong style="color:#111827;">Pass</strong> — step works exactly as expected</span>
                        </div>
                        <div style="display:flex; align-items:flex-start; gap:10px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 14px;">
                            <span style="width:12px; height:12px; border-radius:50%; background:#dc2626; flex-shrink:0; margin-top:3px;"></span>
                            <span style="font-size:13px; color:#374151;"><strong style="color:#111827;">Fail</strong> — something went wrong; a comment is required</span>
                        </div>
                        <div style="display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:10px 14px;">
                            <span style="width:12px; height:12px; border-radius:50%; background:#d97706; flex-shrink:0; margin-top:3px;"></span>
                            <span style="font-size:13px; color:#374151;"><strong style="color:#111827;">Pass + Suggestion</strong> — works but could be improved</span>
                        </div>
                        <div style="display:flex; align-items:flex-start; gap:10px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 14px;">
                            <i class="fa-solid fa-ban" style="color:#b91c1c; flex-shrink:0; margin-top:2px; width:12px;"></i>
                            <span style="font-size:13px; color:#374151;"><strong style="color:#111827;">Block Test</strong> — the system must refuse this action</span>
                        </div>
                    </div>
                    <p style="font-size:13px; color:#4b5563; line-height:1.6;">Work through each section in order. Each step has numbered instructions and a green <strong>Expected Outcome</strong> box. Where a red <strong>Block Test</strong> box appears, the system should <em>refuse</em> that action — this is testing a business rule, not a bug.</p>
                    <p style="font-size:13px; color:#4b5563; line-height:1.6;">Always log out between role switches. Use <kbd style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:1px 7px; font-family:monospace; font-size:12px;">Ctrl+Shift+N</kbd> (incognito) to test two roles in parallel.</p>
                    <div style="background:#e6f5f9; border:1px solid #bae6fd; border-radius:8px; padding:12px 16px; font-size:13px; color:#0c4a6e;">
                        <i class="fa-solid fa-bolt" style="color:#0092b4; margin-right:6px;"></i>
                        <strong>Results are saved to the database.</strong> Fails auto-create a Bug on the Feedback Board. Suggestions auto-create an Improvement — both visible to the developer immediately.
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
            @endif

            {{-- ══ SECTION A ══ --}}
            @include('uat-guide._section', [
                'sectionId'    => 'sectionA',
                'sectionLabel' => 'Section A — Admin (Samy)',
                'roleClass'    => 'bg-blue-100 text-blue-700',
                'roleLabel'    => 'Admin',
                'steps'        => [
                    ['ref' => 'A1',  'title' => 'Login & Dashboard Widgets',
                     'instructions' => [
                        'Log in as Samy. You land on the Dashboard.',
                        'Count the widgets in the top row — there should be exactly <strong>five</strong>:
                         <div class="uat-widget-strip">
                           <div class="uat-widget-pill"><b>Emails</b><span>Unprocessed count</span></div>
                           <div class="uat-widget-pill"><b>Clinical Review</b><span>Case notes flagged for sign-off</span></div>
                           <div class="uat-widget-pill"><b>Enquiries</b><span>Pending enquiry count</span></div>
                           <div class="uat-widget-pill"><b>Invoices Due</b><span>Overdue invoice count</span></div>
                           <div class="uat-widget-pill"><b>Calendar</b><span>Link to appointments</span></div>
                         </div>',
                        'Scroll down — confirm the <strong>Daily Actions</strong> table and <strong>Upcoming Appointments</strong> table are both visible.',
                        'Check the <strong>Overdue Alerts</strong> sidebar (red panel) only appears when associate invoices are overdue — it should be hidden if none are overdue.',
                     ],
                     'outcome' => 'Exactly 5 widgets in the top row. Daily Actions table shows patients with needs_review flagged. No phantom widgets or duplicate rows.'],

                    ['ref' => 'A2',  'title' => 'Create a New Enquiry',
                     'instructions' => [
                        'Go to <strong>Enquiries → New Enquiry</strong>.',
                        'Fill in: Company Name <code>ABC Insurance Ltd</code>, Case Manager name, at least two contacts with different roles.',
                        'Set status to <code>New</code> and save.',
                        'Locate the enquiry in the Enquiries list.',
                     ],
                     'outcome' => 'Enquiry appears in list with status "New". Multiple contacts visible on the enquiry detail page.'],

                    ['ref' => 'A3',  'title' => 'Log a Follow-up Communication',
                     'instructions' => [
                        'Open the enquiry created in A2.',
                        'Log a follow-up: type <code>Initial call with case manager</code>, set date, set a follow-up date for next week.',
                        'Save and confirm it appears in the communications list.',
                        'Go to <strong>Appointments → Calendar</strong> and confirm the follow-up date appears on the correct day next week.',
                        'Update the enquiry status to <code>In Progress</code>.',
                     ],
                     'outcome' => 'Communication logged and visible. Follow-up date appears on the shared Calendar on the correct day. Enquiry shows "In Progress" badge.'],

                    ['ref' => 'A4',  'title' => 'Mark as Qualified Referral (Admin-only Gate)',
                     'instructions' => [
                        'On the enquiry page, scroll to the <strong>"Mark as Qualified Referral"</strong> panel.',
                        'Enter today\'s date as the Qualified Date and click <strong>Mark as Qualified</strong>.',
                        'Confirm the popup.',
                     ],
                     'outcome' => 'Enquiry status changes to "Qualified". Green "Qualified [date]" badge appears. The "Mark as Qualified" form is replaced by the "Convert to Full Record" panel.',
                     'block' => 'Log in as Sheeba (Staff) and verify this button does NOT appear — staff cannot qualify enquiries.'],

                    ['ref' => 'A5',  'title' => 'Convert to Full Record',
                     'instructions' => [
                        'On the qualified enquiry, click <strong>Convert to Full Record</strong>.',
                        'Fill in Company details and Case Manager details.',
                        'Click Convert and confirm the popup.',
                        'Check that a Company record and a Case Manager record now exist.',
                     ],
                     'outcome' => 'Enquiry status changes to "Converted". A <strong>Create Patient</strong> button appears. Company and Case Manager records created.'],

                    ['ref' => 'A6',  'title' => 'Create Patient Record',
                     'instructions' => [
                        'On the converted enquiry, click <strong>Create Patient</strong>.',
                        'Verify Company, Case Manager, and Referral Date are pre-filled.',
                        'Fill in: First Name <code>John</code>, Last Name <code>Smith</code>, Date of Birth, Location, Condition. Add at least one Referrer.',
                        'Save the patient.',
                     ],
                     'outcome' => 'Patient created with status "Enquiry Logged". The enquiry shows a "View Patient" link. Patient appears in the Patients list.'],

                    ['ref' => 'A7',  'title' => 'Create Assessment',
                     'instructions' => [
                        'Open John Smith\'s patient page. Click <strong>Add Assessment</strong>.',
                        'Fill in: Assessor <code>Dr Sarah Jones</code>, Venue, Assessment Date (next week), Fee Agreed <code>£350</code> with PDF, Assessment Cost <code>£350</code> with PDF, Special Instructions.',
                        'Save the assessment.',
                        'Go to <strong>Appointments → Calendar</strong> and confirm the assessment date appears on the correct day next week.',
                     ],
                     'outcome' => 'Assessment saved. Patient status changes to "Assessment Scheduled". Assessment date visible on the shared Calendar. Both documents downloadable.'],

                    ['ref' => 'A8',  'title' => 'Create Assessment Invoice',
                     'instructions' => [
                        'On the patient/assessment page, click <strong>Create Assessment Invoice</strong>.',
                        'Set recipient type <code>Case Manager Company</code>, fill recipient name and email, total <code>£350</code>. Save.',
                        'Note the auto-generated invoice number.',
                     ],
                     'outcome' => 'VTA Invoice created with a unique number (e.g. VTA-2026-001). Linked to the assessment record. Appears in the patient timeline and VTA Invoices list.'],

                    ['ref' => 'A9',  'title' => 'Mark Report Sent (Document Required)',
                     'instructions' => [
                        '<strong>First attempt:</strong> Tick "Report Sent" WITHOUT uploading a report document and click Save.',
                        '<strong>Second attempt:</strong> Upload a report PDF, tick "Report Sent", and save.',
                     ],
                     'outcome' => 'Second attempt: assessment saved with Report Sent = Yes. Patient status changes to "Report Sent". Report document is downloadable.',
                     'block' => 'First attempt must be refused: "A report document must be uploaded before marking as sent." Patient status must NOT change.'],

                    ['ref' => 'A10', 'title' => 'Create Cost Estimation',
                     'instructions' => [
                        'On the patient page, click <strong>Add Cost Estimation</strong>.',
                        'Fill in: Amount <code>£4,800</code>, Sessions <code>12</code>, Duration <code>6 months</code>, Sent Date today. Note version = <code>v1</code>.',
                        'Create a second estimation: Amount <code>£5,200</code>, Sessions <code>14</code>. Confirm it is auto-labelled <code>v2</code>.',
                     ],
                     'outcome' => 'First estimation saved as v1 → patient status "Cost Estimation Sent". Second saved as v2. Both visible on patient page.'],

                    ['ref' => 'A11', 'title' => 'Create Funding Cycle',
                     'instructions' => [
                        'Click <strong>Add Funding Cycle</strong>. Fill in: Approved Amount <code>£5,200</code>, Sessions <code>14</code>, Funder <code>ABC Insurance Ltd</code>, upload approval document PDF. Tick "Is Active" and save.',
                        'Try creating a <strong>second active</strong> funding cycle.',
                     ],
                     'outcome' => 'First cycle created → patient status "Funding Approved". Remaining balance shown as £5,200.',
                     'block' => 'Second active cycle must be refused: "This patient already has an active funding cycle."'],

                    ['ref' => 'A12', 'title' => 'Allocate an Associate',
                     'instructions' => [
                        'On the patient page → Associates section → click <strong>Add Associate</strong>.',
                        'Select Kate, role <code>Treatment</code>, start date today. Save.',
                        'Check the Communications / Timeline for the auto-logged entry.',
                        'Try adding Kate again with role "Treatment".',
                     ],
                     'outcome' => 'Kate allocated. Auto communication logged: "Kate allocated as Treatment on [date]".',
                     'block' => 'Duplicate role must be refused: "Patient already has an active Treatment associate."'],

                    ['ref' => 'A13', 'title' => 'Create Treatment VTA Invoice → Treatment Active',
                     'instructions' => [
                        'Go to <strong>Finance → VTA Invoices → New Invoice</strong>. Select John Smith and Funding Cycle 1.',
                        'Fill in: Sessions <code>4</code>, Session Amount <code>£400</code>, Total <code>£1,600</code>, invoice date today.',
                        'Save. Check funding cycle remaining balance is now £3,600.',
                        '<strong>First attempt:</strong> Mark as "Sent" WITHOUT uploading a document.',
                        '<strong>Second attempt:</strong> Upload invoice PDF and mark as Sent.',
                     ],
                     'outcome' => 'Invoice marked Sent → patient status changes to "Treatment Active". Funding balance reduces to £3,600.',
                     'block' => 'First attempt: cannot mark Sent without an uploaded invoice document.'],

                    ['ref' => 'A14', 'title' => 'Log an Associate Invoice',
                     'instructions' => [
                        'Go to <strong>Finance → Associate Invoices → Log Invoice</strong>.',
                        'Select Kate, John Smith, Funding Cycle 1. Fill: Sessions <code>4</code>, Travel Miles <code>20</code>, Total <code>£1,210</code>, invoice date today.',
                        'Save. Confirm Due Date is auto-calculated as 28 days from invoice date.',
                        'Update status to <code>Verified</code>.',
                     ],
                     'outcome' => 'Invoice logged with correct due date. Status moves Received → Verified → Paid.'],

                    ['ref' => 'A15', 'title' => 'Create Case Note and Sign Off',
                     'instructions' => [
                        'Go to <strong>Case Notes → New Case Note</strong>. Select John Smith, Kate, last week\'s session date. Add content. Save (status: Pending Sign-off).',
                        'Open the case note. Click <strong>Sign Off</strong> and confirm.',
                        'Refresh and verify the signed-off state.',
                     ],
                     'outcome' => '"Signed off by Samy [date and time]" shown in green. Edit and Delete buttons have disappeared — note is locked.'],

                    ['ref' => 'A16', 'title' => 'Clinical Head Review — Dashboard Widget',
                     'instructions' => [
                        'Create a second case note for John Smith (unsigned — leave in Pending Sign-off).',
                        'Return to the <strong>Dashboard</strong> and check the <strong>Clinical Review</strong> widget count.',
                        'Sign it off. Confirm the Clinical Review count decreases.',
                     ],
                     'outcome' => 'Dashboard Clinical Review count reflects current unsigned/flagged notes. Count decreases when notes are signed off.'],

                    ['ref' => 'A17', 'title' => 'Book an Appointment',
                     'instructions' => [
                        'Go to <strong>Appointments → New Appointment</strong>.',
                        'Select John Smith, Kate, activity type <code>Video Consultation</code>, date and time (next week), location. Save.',
                        'Go to <strong>Appointments → Calendar</strong> and confirm the appointment appears on the correct date.',
                        'Check the <strong>Upcoming Appointments</strong> table on the Dashboard shows this appointment (if within 7 days).',
                     ],
                     'outcome' => 'Appointment visible on the shared Calendar. Appears in the patient page timeline. If within 7 days, visible in Dashboard Upcoming Appointments.'],

                    ['ref' => 'A18', 'title' => 'Reports — All Four Reports',
                     'instructions' => [
                        'Go to <strong>Reports → Funding Balance Summary</strong>. Verify John Smith appears with Cycle 1 balance.',
                        'Go to <strong>Reports → Financial Summary</strong>. Set date range to current month. Verify amounts match A13 and A14.',
                        'Go to <strong>Reports → Patients by Status</strong>. Verify "Treatment Active" count includes John Smith.',
                        'Go to <strong>Reports → Associate Activity</strong>. Set date range to current month. Verify Kate appears.',
                     ],
                     'outcome' => 'All four reports load without errors. Data reflects the test records. Date range filters work.'],

                    ['ref' => 'A19', 'title' => 'Settings — Activity Types, Permissions, Rates',
                     'instructions' => [
                        'Go to <strong>Settings</strong>. Add a new Activity Type: <code>Home Visit</code>. Confirm it appears in the appointments dropdown.',
                        'Under Document Permissions: Associates can view "Session Notes" but NOT "Assessment Reports". Save.',
                        'Check Kate\'s profile has session rate and travel rate configured.',
                     ],
                     'outcome' => 'New activity type available when booking appointments. Document permissions respected in Kate\'s portal.'],

                    ['ref' => 'A20', 'title' => 'Email Intake — Log and Link an Email',
                     'instructions' => [
                        'Go to <strong>Email Intake</strong>. Click <strong>Log Manual Email</strong>.',
                        'Enter sender <code>ABC Insurance</code>, subject <code>Re: John Smith — approval confirmed</code>, date today. Save.',
                        'Find the email in the Unprocessed list. Click <strong>Link</strong> and link it to John Smith.',
                        'Confirm it is no longer unprocessed. Check the Dashboard Emails widget count decreased by 1.',
                     ],
                     'outcome' => 'Email logged then linked. Dashboard Emails count decreases. Linked email visible in patient\'s communication history.'],

                    ['ref' => 'A21', 'title' => 'Feedback & Questions Board',
                     'instructions' => [
                        'Go to <strong>Feedback & Questions</strong>.',
                        'Questions tab: confirm previously answered questions (Q1–Q18) show existing answers in green boxes with "Update your answer" link.',
                        'Scroll to the <strong>Pre-UAT Planning</strong> section — confirm 15 new questions (P3-Q1 through P3-Q15) appear.',
                        'Click <strong>"Answer this question"</strong> on P3-Q1. Type a response and save.',
                        'Click <strong>"Update your answer"</strong> — the textarea should reappear pre-filled for editing.',
                     ],
                     'outcome' => 'Q1–Q18 answers intact. Pre-UAT questions answerable. Update flow shows existing answer and saves edits.'],

                    ['ref' => 'A22', 'title' => 'Patient Timeline & Document Upload',
                     'instructions' => [
                        'Open John Smith\'s patient page. Scroll to the <strong>Timeline</strong> section.',
                        'Verify it shows all activity in reverse date order: communications, documents, case notes, appointments, VTA invoices.',
                        'Upload a document (any PDF). Confirm it appears in the timeline immediately and is downloadable.',
                        'Toggle the <strong>Needs Review</strong> flag and check the Dashboard Daily Actions table updates.',
                     ],
                     'outcome' => 'Unified timeline shows all events in correct order. Document upload works. Needs Review toggle immediately affects Dashboard.'],

                    ['ref' => 'A23', 'title' => 'Patient Status — On Hold',
                     'instructions' => [
                        'Open John Smith\'s patient page. Status is currently <strong>Treatment Active</strong>.',
                        'Change status to <strong>On Hold</strong>. Confirm the change is accepted.',
                        'Verify the status badge on the patient card updates to "On Hold".',
                        'Change status back to <strong>Treatment Active</strong>.',
                     ],
                     'outcome' => 'Status transitions Treatment Active → On Hold → Treatment Active all succeed. Status badge reflects each change.'],

                    ['ref' => 'A24', 'title' => 'Case Closure — Unpaid Invoice Block',
                     'instructions' => [
                        'Ensure John Smith has at least one VTA invoice that is <strong>not</strong> marked as Paid.',
                        'Attempt to change John Smith\'s status to <strong>Case Closed</strong>.',
                        'Read the error message shown.',
                     ],
                     'outcome' => '',
                     'block' => 'System must refuse Case Closed if any unpaid VTA invoices exist, showing the count and total amount owed.'],

                    ['ref' => 'A25', 'title' => 'Funding Balance Warning',
                     'instructions' => [
                        'Open a patient who has used 80% or more of their approved funding amount.',
                        'Scroll to the <strong>Funding Overview</strong> section.',
                        'Verify the amber warning banner appears showing the remaining percentage.',
                        'If a patient has fully exhausted their funding, verify a red "Funding exhausted" banner appears instead.',
                     ],
                     'outcome' => 'Amber warning shows when ≥80% of funding is used. Red banner shows when 100% is used. No banner when usage is below 80%.'],
                ],
            ])

            {{-- ══ SECTION B ══ --}}
            @include('uat-guide._section', [
                'sectionId'    => 'sectionB',
                'sectionLabel' => 'Section B — Staff (Sheeba)',
                'roleClass'    => 'bg-purple-100 text-purple-700',
                'roleLabel'    => 'Staff',
                'note'         => 'Log out as Samy and log in as Sheeba for all steps in this section.',
                'steps'        => [
                    ['ref' => 'B1', 'title' => 'Staff Navigation — Restricted Menus',
                     'instructions' => [
                        'Log in as Sheeba. Check the navigation menu.',
                        'Confirm visible: Dashboard, Enquiries, Companies, Patients, Communications, Email Intake, Appointments, Case Notes.',
                        'Confirm NOT visible: Finance, Settings, Reports, Feedback & Questions.',
                        'Attempt to navigate directly to <code>/finance</code> and <code>/settings</code> via the address bar.',
                     ],
                     'outcome' => 'Staff sees only clinical/operational menus.',
                     'block' => 'Direct navigation to /finance and /settings must return 403 Forbidden.'],

                    ['ref' => 'B2', 'title' => 'Staff Cannot Qualify an Enquiry',
                     'instructions' => [
                        'Create a fresh enquiry as Sheeba.',
                        'Open the new enquiry. Look for the "Mark as Qualified" panel.',
                        'Attempt to POST to <code>/enquiries/[id]/qualify</code> directly.',
                     ],
                     'outcome' => 'Sheeba can create enquiries and log communications.',
                     'block' => '"Mark as Qualified" panel must NOT appear. Any direct POST to the qualify route must return 403.'],

                    ['ref' => 'B3', 'title' => 'Staff Patient Management',
                     'instructions' => [
                        'Open John Smith. Upload a document.',
                        'Attempt to delete a document uploaded by Samy — note the result.',
                        'Toggle the "Needs Review" flag on John Smith.',
                        'Log a communication: follow-up call with a follow-up date set for next week.',
                        'Go to <strong>Appointments → Calendar</strong> and confirm the follow-up date appears on the correct day.',
                     ],
                     'outcome' => 'Sheeba can upload documents, log communications, toggle Needs Review, and the follow-up date appears on the Calendar. Document deletion restrictions follow permissions set in Settings.'],

                    ['ref' => 'B4', 'title' => 'Staff Email Intake',
                     'instructions' => [
                        'Go to <strong>Email Intake</strong>. Log a manual email and link it to John Smith.',
                        'Confirm it moves from unprocessed to processed.',
                     ],
                     'outcome' => 'Staff can fully process email intake the same as Admin.'],
                ],
            ])

            {{-- ══ SECTION C ══ --}}
            @include('uat-guide._section', [
                'sectionId'    => 'sectionC',
                'sectionLabel' => 'Section C — Associate (Kate)',
                'roleClass'    => 'bg-green-100 text-green-700',
                'roleLabel'    => 'Associate',
                'note'         => 'Log out as Sheeba and log in as Kate. Kate lands on the Associate Portal — a completely separate interface.',
                'steps'        => [
                    ['ref' => 'C1', 'title' => 'Associate Portal Dashboard',
                     'instructions' => [
                        'Log in as Kate. Confirm she lands on the Associate Portal (not the main dashboard).',
                        'Check three widgets: <strong>My Patients</strong>, <strong>Upcoming Appointments</strong> (next 14 days), <strong>Awaiting Case Notes</strong>.',
                        'Confirm John Smith appears in My Patients.',
                        'Attempt to navigate to <code>/dashboard</code> and <code>/enquiries</code>.',
                     ],
                     'outcome' => 'Associate Portal loads with correct counts. John Smith visible in My Patients.',
                     'block' => 'Any access to admin routes (/dashboard, /enquiries, /finance, /settings) must return 403.'],

                    ['ref' => 'C2', 'title' => 'View Patient Details & Documents',
                     'instructions' => [
                        'Click on John Smith in Kate\'s patient list.',
                        'Confirm Kate can see: patient details, her appointments with John, her own case notes, permitted documents.',
                        'Confirm Kate cannot see: VTA invoices, funding cycles, cost estimations, assessment fee details.',
                        'Download a permitted document. Attempt to access a restricted document type.',
                     ],
                     'outcome' => 'Kate sees clinical information only. Financial and assessment fee data not visible. Document permissions respected.'],

                    ['ref' => 'C3', 'title' => 'Upload Case Note — Three-Stage Workflow',
                     'instructions' => [
                        'On John Smith\'s page, find the Case Notes section showing three groups: Draft, Revision, Final.',
                        'Under <strong>Draft</strong>: upload a session note PDF (session date last week).',
                        'Under <strong>Final</strong>: upload a final report PDF (session date this week).',
                        'On the Final note upload, tick <strong>"Flag for Clinical Head Review"</strong> before submitting.',
                     ],
                     'outcome' => 'Draft note appears in Draft section. Final note flagged for review. On the main Dashboard (Samy), the Clinical Review widget count increases by 1.'],

                    ['ref' => 'C4', 'title' => 'Associate Calendar',
                     'instructions' => [
                        'Go to <strong>My Calendar</strong> in the Associate Portal.',
                        'Confirm the appointment booked in A17 appears on Kate\'s calendar.',
                        'Confirm Kate cannot create or delete appointments from here.',
                     ],
                     'outcome' => 'Kate\'s calendar shows only her own appointments. Read-only — no create/edit/delete controls visible.'],

                    ['ref' => 'C5', 'title' => 'Associate Invoice View',
                     'instructions' => [
                        'On John Smith\'s page, scroll to the Invoices section.',
                        'Confirm the associate invoice from A14 appears. Confirm no VTA invoices or other associates\' invoices are visible.',
                     ],
                     'outcome' => 'Kate sees only her own invoices per patient. Read-only.'],
                ],
            ])

            {{-- ══ SECTION D ══ --}}
            @include('uat-guide._section', [
                'sectionId'    => 'sectionD',
                'sectionLabel' => 'Section D — Case Manager (Sarah / Michael)',
                'roleClass'    => 'bg-pink-100 text-pink-700',
                'roleLabel'    => 'Case Manager',
                'note'         => 'Log out as Kate and log in as Sarah. Case Managers land on the Case Manager Portal — read-only access to their company\'s patients only.',
                'steps'        => [
                    ['ref' => 'D1', 'title' => 'Case Manager Portal — Own Company Only',
                     'instructions' => [
                        'Log in as Sarah. Confirm she lands on the Case Manager Portal dashboard.',
                        'Check the patient list — only Sarah\'s company patients should appear.',
                        'Log out and log in as Michael. Confirm Michael sees different patients (his company only).',
                     ],
                     'outcome' => 'Each case manager sees only their own company\'s patients. Lists correctly isolated by company.'],

                    ['ref' => 'D2', 'title' => 'Case Manager — Read-only Access',
                     'instructions' => [
                        'As Sarah, open John Smith\'s patient record.',
                        'Confirm: can view patient status, permitted documents, case notes summary.',
                        'Confirm: no Edit, Delete, or Upload buttons anywhere on the patient page.',
                        'Download a permitted document. Attempt to access a restricted document type.',
                     ],
                     'outcome' => 'Sarah can view and download permitted documents. No create/edit/delete controls visible.'],

                    ['ref' => 'D3', 'title' => 'Case Manager — Access Control to Admin Routes',
                     'instructions' => [
                        'As Sarah, navigate directly to: <code>/enquiries</code>, <code>/patients</code>, <code>/finance</code>, <code>/settings</code>, <code>/vta-invoices</code>.',
                        'Try accessing a patient from a different company via the case manager portal.',
                     ],
                     'outcome' => 'Case manager is completely isolated to their portal and their company\'s data.',
                     'block' => 'All admin routes must return 403. Cross-company patient access must also return 403.'],

                    ['ref' => 'D4', 'title' => 'Case Manager — Add Case Note',
                     'instructions' => [
                        'As Sarah, open John Smith\'s page. Find the Case Notes section.',
                        'If case note submission is available, add a short note and save.',
                        'Log back in as Samy and confirm the note appears on John Smith\'s admin page.',
                     ],
                     'outcome' => 'If enabled: note appears on patient page for admin. Case manager cannot see financial data when viewing the patient page.'],
                ],
            ])

            {{-- ══ FINAL ══ --}}
            @include('uat-guide._section', [
                'sectionId'    => 'sectionF',
                'sectionLabel' => 'Final Verification — Log back in as Samy',
                'roleClass'    => 'bg-blue-100 text-blue-700',
                'roleLabel'    => 'Admin',
                'note'         => 'Complete the cycle by logging back in as Samy to verify everything Kate and Sarah did is reflected in the admin portal.',
                'steps'        => [
                    ['ref' => 'F1', 'title' => 'Verify Clinical Review Count Updated',
                     'instructions' => [
                        'Log in as Samy. Check the Clinical Review dashboard widget count — should have increased by 1 from Kate\'s flagged Final note (C3).',
                        'Go to Case Notes and find the note Kate flagged. Confirm status is Pending Sign-off with needs_review = true.',
                        'Sign it off. Confirm the Clinical Review count decreases by 1.',
                     ],
                     'outcome' => 'Full case note review loop works: Associate flags → Dashboard shows count → Samy signs off → Count decreases.'],

                    ['ref' => 'F2', 'title' => 'Full Patient Timeline — Complete Picture',
                     'instructions' => [
                        'Open John Smith\'s patient page.',
                        'Scroll the Timeline — confirm entries from all stages: communication (A3), assessment (A7), cost estimation (A10), funding cycle (A11), associate allocation (A12), VTA invoice (A13), Kate\'s case notes (A15, C3), appointment (A17), document upload (A22).',
                        'Confirm patient status is <strong>Treatment Active</strong>.',
                        'Confirm Funding Cycle remaining balance is correctly reduced from the invoice in A13.',
                     ],
                     'outcome' => 'Patient page shows a complete unified timeline. Status is Treatment Active. Financial balance is accurate.'],
                ],
            ])

            {{-- ══ SECTION G — REFERRAL FLOW ══ --}}
            @include('uat-guide._section', [
                'sectionId'    => 'sectionG',
                'sectionLabel' => 'Section G — Full Referral Flow (Enquiry → Patient)',
                'roleClass'    => 'bg-emerald-100 text-emerald-700',
                'roleLabel'    => 'Admin',
                'note'         => 'This section tests the complete E→R→P journey introduced in the latest build. Log in as Samy throughout. Use Georgios (georgios@test.com / password) to test the associate portal steps.',
                'steps'        => [
                    ['ref' => 'G1', 'title' => 'Create a New Enquiry',
                     'instructions' => [
                        'Log in as Samy. Go to <strong>Enquiries → New Enquiry</strong>.',
                        'Create a new enquiry: company <code>Test Insurance Ltd</code>, patient first name <code>Test</code>, last name <code>Patient</code>.',
                        'Set status to <strong>In Progress</strong> and save.',
                        'Confirm the enquiry appears with an auto-generated VTA-xxx reference.',
                     ],
                     'outcome' => 'Enquiry created with VTA-xxx reference. Status shows "In Progress". No referral or patient record yet.'],

                    ['ref' => 'G2', 'title' => 'Promote to Referral',
                     'instructions' => [
                        'Open the enquiry created in G1.',
                        'Click <strong>"Promote to Referral"</strong>.',
                        'Confirm a Referral record is created with <em>the same VTA-xxx reference</em>.',
                        'Confirm the enquiry status has updated to <strong>Qualified</strong>.',
                        'Go to Referrals → confirm the new referral appears with status <strong>In Progress</strong>.',
                     ],
                     'outcome' => 'Referral exists with the same VTA-xxx ref. Enquiry status = Qualified. Referral status = In Progress.'],

                    ['ref' => 'G3', 'title' => 'Block Test — Create Referral Directly',
                     'instructions' => [
                        'Go to <strong>Referrals</strong> and look for any "New Referral" button.',
                     ],
                     'outcome' => '<span class="text-red-700 font-semibold">Block Test:</span> No "New Referral" button should exist. Referrals can only be created by promoting a qualified enquiry. If a "New Referral" button is present, this is a defect.'],

                    ['ref' => 'G4', 'title' => 'Record Go-ahead & Assign Associate',
                     'instructions' => [
                        'Open the referral from G2.',
                        'Click <strong>"Record Go-ahead"</strong> (or equivalent action). Enter a visit approval date.',
                        'Assign associate <strong>Georgios Tsiknas</strong>.',
                        'Confirm referral status updates to <strong>Assessment</strong>.',
                        'Log in as Georgios (georgios@test.com / password) → confirm the referral appears in <strong>My Referrals</strong> on the associate portal.',
                     ],
                     'outcome' => 'Referral status = Assessment. Georgios can see the referral in his portal with patient details and special instructions.'],

                    ['ref' => 'G5', 'title' => 'Log Assessment Session from Associate Portal',
                     'instructions' => [
                        'Still logged in as Georgios.',
                        'Open the referral in the associate portal.',
                        'Log a new session: activity type <strong>Assessment</strong>, date today, duration 60 minutes, location "Patient\'s home".',
                        'Log back in as Samy → open the referral → confirm the session appears in the Sessions section.',
                     ],
                     'outcome' => 'Session logged by associate is visible to Samy on the referral page. Session count increases by 1.'],

                    ['ref' => 'G6', 'title' => 'Upload Document & Request Revision',
                     'instructions' => [
                        'Log in as Georgios → open the referral → upload any small PDF as a document (e.g. a test file).',
                        'Log back in as Samy → open the referral → find the document → click <strong>"Request Revision"</strong> and enter a note: <code>Please add the date to page 1.</code>',
                        'Log back in as Georgios → confirm an <strong>amber revision alert</strong> is shown on the referral with the note text.',
                     ],
                     'outcome' => 'Document revision request visible to associate with Samy\'s note. Amber banner appears in the associate portal.'],

                    ['ref' => 'G7', 'title' => 'Submit Proposal',
                     'instructions' => [
                        'Log in as Samy → open the referral.',
                        'Click <strong>"Submit Proposal"</strong>.',
                        'Confirm referral status updates to <strong>Proposal Submitted</strong>.',
                     ],
                     'outcome' => 'Referral status = Proposal Submitted. Convert to Patient button is not visible yet.'],

                    ['ref' => 'G8', 'title' => 'Approve Proposal',
                     'instructions' => [
                        'On the referral page, click <strong>"Approve Proposal"</strong>.',
                        'Confirm status updates to <strong>Approved</strong>.',
                        'Confirm a green <strong>"Convert to Patient"</strong> button now appears.',
                     ],
                     'outcome' => 'Referral status = Approved. Convert to Patient button visible.'],

                    ['ref' => 'G9', 'title' => 'Convert to Patient',
                     'instructions' => [
                        'Click <strong>"Convert to Patient"</strong> on the approved referral.',
                        'Complete any required patient fields and save.',
                        'Confirm a Patient record is created with the <em>same VTA-xxx reference</em> as the enquiry and referral.',
                        'Confirm the referral status changes to <strong>Converted</strong>.',
                        'Open the patient → confirm the enquiry and referral IDs are both linked.',
                     ],
                     'outcome' => 'Patient created with the same VTA-xxx ref. Referral status = Converted. Patient record shows both enquiry_id and referral_id links.'],

                    ['ref' => 'G10', 'title' => 'Audit Trail Verification',
                     'instructions' => [
                        'Go to <strong>Reports → Audit → Patient Audit</strong> and open the patient from G9.',
                        'Confirm the <strong>Referral Stage card</strong> is visible, showing the referral ref, milestones (go-ahead date, proposal submitted, approval date, conversion date), and the session logged in G5.',
                        'Go to <strong>Reports → Audit → Associate Audit</strong> → open Georgios\'s audit.',
                        'Confirm the referral sessions section shows the session from G5.',
                     ],
                     'outcome' => 'Patient audit shows full referral history. Associate audit shows referral sessions. The complete E→R→P journey is visible end-to-end.'],
                ],
            ])

        </div>
    </div>
</x-app-layout>
