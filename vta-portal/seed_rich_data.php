<?php
/**
 * Rich seed data for VTA Portal audit testing.
 * Run: C:\xampp\php\php.exe seed_rich_data.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{Associate, AssociateComplianceDocument, FundingCycle};

$adminUserId = 4;
$staffUserId = 2;

function ts(int $daysAgo, string $time = '09:00:00'): string {
    return Carbon::now()->subDays($daysAgo)->setTimeFromTimeString($time)->format('Y-m-d H:i:s');
}
function tsDate(int $daysAgo): string {
    return Carbon::now()->subDays($daysAgo)->toDateString();
}
function futureDt(int $daysAhead, int $hour = 10): string {
    return Carbon::now()->addDays($daysAhead)->setTime($hour, 0)->format('Y-m-d H:i:s');
}

$associates = Associate::all();

// ─── 1. Associate induction events ───────────────────────────────────────────
echo "Logging associate inductions...\n";
foreach ($associates as $assoc) {
    $oc = Carbon::parse($assoc->created_at);
    DB::table('activity_logs')->insert([
        'user_id'      => $adminUserId,
        'subject_type' => 'Associate',
        'subject_id'   => $assoc->id,
        'patient_id'   => null,
        'associate_id' => $assoc->id,
        'action'       => 'created',
        'description'  => "{$assoc->name} was inducted as a VTA associate ({$assoc->region})",
        'metadata'     => json_encode(['region' => $assoc->region]),
        'occurred_at'  => $oc->format('Y-m-d H:i:s'),
    ]);
}

// ─── 2. Compliance documents ──────────────────────────────────────────────────
echo "Adding compliance documents...\n";
$complianceSets = [
    1 => [
        ['DBS Check',                 '2027-06-01'],
        ['Professional Registration', '2026-09-30'],
        ['Contract',                  null],
        ['CSP Membership',            '2026-08-01'],
    ],
    2 => [
        ['DBS Check',                 '2027-03-15'],
        ['Professional Registration', '2026-07-20'], // expiring soon
        ['Contract',                  null],
        ['CSP Membership',            '2026-11-30'],
        ['Insurance',                 '2026-12-31'],
    ],
    3 => [
        ['DBS Check',                 '2025-12-01'], // EXPIRED
        ['Professional Registration', '2026-10-31'],
        ['Contract',                  null],
    ],
    4 => [
        ['DBS Check',                 '2027-01-20'],
        ['Professional Registration', '2027-04-30'],
        ['Contract',                  null],
        ['CSP Membership',            '2026-09-01'],
    ],
    5 => [
        ['DBS Check',                 '2026-08-10'], // expiring soon
        ['Professional Registration', '2027-02-28'],
        ['Contract',                  null],
    ],
    6 => [
        ['DBS Check',                 '2027-05-01'],
        ['Professional Registration', '2026-12-31'],
        ['Contract',                  null],
        ['CSP Membership',            '2027-01-31'],
        ['Insurance',                 '2027-01-31'],
    ],
    7 => [
        ['DBS Check',                 '2026-11-30'],
        ['Professional Registration', '2027-03-31'],
        ['Contract',                  null],
    ],
    8 => [
        ['DBS Check',                 '2027-04-15'],
        ['Professional Registration', '2026-08-31'],
        ['Contract',                  null],
        ['CSP Membership',            '2026-10-31'],
    ],
    9 => [
        ['DBS Check',                 '2027-06-30'],
        ['Professional Registration', '2027-06-30'],
        ['Contract',                  null],
        ['CSP Membership',            '2027-06-30'],
        ['Insurance',                 '2027-06-30'],
    ],
];

foreach ($complianceSets as $associateId => $docs) {
    foreach ($docs as [$type, $expiry]) {
        $daysAgo = rand(60, 300);
        $doc = AssociateComplianceDocument::create([
            'associate_id'  => $associateId,
            'document_type' => $type,
            'document_path' => 'compliance/' . strtolower(str_replace(' ', '_', $type)) . "_{$associateId}.pdf",
            'expiry_date'   => $expiry,
            'notes'         => null,
            'uploaded_by'   => $adminUserId,
            'created_at'    => ts($daysAgo),
            'updated_at'    => ts($daysAgo),
        ]);
        $assocName = $associates->find($associateId)->name ?? "Associate #{$associateId}";
        DB::table('activity_logs')->insert([
            'user_id'      => $adminUserId,
            'subject_type' => 'AssociateComplianceDocument',
            'subject_id'   => $doc->id,
            'patient_id'   => null,
            'associate_id' => $associateId,
            'action'       => 'compliance_uploaded',
            'description'  => "{$type} uploaded for {$assocName}",
            'metadata'     => json_encode(['document_type' => $type, 'expiry_date' => $expiry]),
            'occurred_at'  => ts($daysAgo),
        ]);
    }
}

// ─── 3. Patients with full journeys ──────────────────────────────────────────
echo "Creating patients...\n";

$companyIds     = DB::table('companies')->pluck('id')->toArray() ?: [null];
$caseManagerIds = DB::table('case_managers')->pluck('id')->toArray() ?: [null];

$patientDefs = [
    // [fn, ln, dob, employer, associateId, status, daysAgoEnquiry]
    // CLOSED / DISCHARGED
    ['Priya',   'Nair',      '1985-04-12', 'Royal Mail',      1, 'Case Closed',               180],
    ['Thomas',  'Blackwell', '1979-11-03', 'NHS Trust',       2, 'Discharged',                150],
    ['Fatima',  'Al-Hassan', '1992-07-22', 'Tesco PLC',       3, 'Case Closed',               120],
    // TREATMENT ACTIVE
    ['Marcus',  'Osei',      '1988-03-17', 'DHL Logistics',   4, 'Treatment Active',           90],
    ['Chloe',   'Whitfield', '1995-08-09', 'Capita Group',    5, 'Treatment Active',           75],
    ['Raj',     'Patel',     '1983-01-25', 'BT Group',        6, 'Treatment Active',           60],
    ['Amelia',  'Frost',     '1990-12-31', 'HSBC Bank',       7, 'Treatment Active',           55],
    // FUNDING STAGE
    ['David',   'Kimani',    '1977-09-14', 'Amazon UK',       8, 'Funding Approved',           40],
    ['Sophie',  'Leclair',   '1986-05-28', 'Virgin Media',    9, 'Awaiting Funding Approval',  35],
    // ASSESSMENT STAGE
    ['Ibrahim', 'Musa',      '1993-02-18', 'Barclays Bank',   1, 'Assessment Completed',       25],
    // ENQUIRY STAGE
    ['Zara',    'Collins',   '1998-10-05', 'Network Rail',    2, 'Response Sent',              10],
    ['Connor',  'Walsh',     '1981-06-30', 'Unilever UK',     3, 'Awaiting LOI',               7],
];

$funders = ['AXA Health','Bupa','Aviva','Vitality','Cigna'];
$locations = ['Video Call','On-site','Clinic'];
$noteTypes = ['Session Note','Progress Note'];

foreach ($patientDefs as [$fn, $ln, $dob, $employer, $assocId, $status, $enqDay]) {
    $source  = collect(['Email','Phone','Referral Letter','Website','Word of Mouth'])->random();
    $compId  = collect($companyIds)->random();
    $cmId    = collect($caseManagerIds)->random();
    $assoc   = $associates->find($assocId);
    $name    = "{$fn} {$ln}";

    // ── Enquiry ──────────────────────────────────────────────────────────────
    $enqId = DB::table('enquiries')->insertGetId([
        'enquirer_name'   => $name,
        'company_name'    => $employer,
        'company_id'      => $compId,
        'case_manager_id' => $cmId,
        'source'          => $source,
        'reason'          => "Referral for occupational health assessment — {$fn} {$ln} ({$employer})",
        'enquiry_date'    => tsDate($enqDay),
        'status'          => 'Converted',
        'notes'           => null,
        'created_by'      => $staffUserId,
        'created_at'      => ts($enqDay),
        'updated_at'      => ts($enqDay),
    ]);

    DB::table('activity_logs')->insert([
        'user_id'      => $staffUserId,
        'subject_type' => 'Enquiry',
        'subject_id'   => $enqId,
        'patient_id'   => null,
        'associate_id' => null,
        'action'       => 'created',
        'description'  => "New enquiry received for {$name} via {$source}",
        'metadata'     => json_encode(['source' => $source, 'employer' => $employer]),
        'occurred_at'  => ts($enqDay, '09:15:00'),
    ]);

    if ($enqDay > 7) {
        DB::table('activity_logs')->insert([
            'user_id'      => $staffUserId,
            'subject_type' => 'Enquiry',
            'subject_id'   => $enqId,
            'patient_id'   => null,
            'associate_id' => null,
            'action'       => 'status_changed',
            'description'  => "Response sent to enquiry for {$name}",
            'metadata'     => json_encode(['from' => 'New', 'to' => 'In Progress']),
            'occurred_at'  => ts($enqDay - 1, '10:30:00'),
        ]);
    }

    if ($enqDay > 12) {
        DB::table('activity_logs')->insert([
            'user_id'      => $staffUserId,
            'subject_type' => 'Enquiry',
            'subject_id'   => $enqId,
            'patient_id'   => null,
            'associate_id' => null,
            'action'       => 'qualified',
            'description'  => "Enquiry qualified and converted to patient for {$name}",
            'metadata'     => json_encode(['employer' => $employer]),
            'occurred_at'  => ts($enqDay - 3, '11:00:00'),
        ]);
    }

    // ── Patient ───────────────────────────────────────────────────────────────
    $patId = DB::table('patients')->insertGetId([
        'first_name'              => $fn,
        'last_name'               => $ln,
        'date_of_birth'           => $dob,
        'enquiry_id'              => $enqId,
        'case_manager_id'         => $cmId,
        'status'                  => $status,
        'location'                => $assoc->region ?? 'UK',
        'referral_date'           => tsDate($enqDay - 3),
        'condition'               => "Musculoskeletal — occupational health assessment required.",
        'invoice_recipient_type'  => 'Case Manager Company',
        'invoice_recipient_name'  => $employer,
        'needs_review'            => 0,
        'notes'                   => "Patient referred by {$employer}.",
        'created_by'              => $staffUserId,
        'created_at'              => ts($enqDay - 3),
        'updated_at'              => ts(1),
    ]);

    DB::table('activity_logs')->insert([
        'user_id'      => $staffUserId,
        'subject_type' => 'Patient',
        'subject_id'   => $patId,
        'patient_id'   => $patId,
        'associate_id' => null,
        'action'       => 'created',
        'description'  => "Patient record created for {$name} ({$employer})",
        'metadata'     => json_encode(['enquiry_id' => $enqId]),
        'occurred_at'  => ts($enqDay - 3, '11:30:00'),
    ]);

    // Assign associate
    DB::table('patient_associates')->insert([
        'patient_id'   => $patId,
        'associate_id' => $assocId,
        'start_date'   => tsDate($enqDay - 5),
        'end_date'     => in_array($status, ['Case Closed','Discharged']) ? tsDate(5) : null,
        'created_at'   => ts($enqDay - 5),
    ]);
    DB::table('activity_logs')->insert([
        'user_id'      => $adminUserId,
        'subject_type' => 'Patient',
        'subject_id'   => $patId,
        'patient_id'   => $patId,
        'associate_id' => $assocId,
        'action'       => 'updated',
        'description'  => "{$assoc->name} assigned as treating associate for {$name}",
        'metadata'     => json_encode(['associate_id' => $assocId]),
        'occurred_at'  => ts($enqDay - 5, '14:00:00'),
    ]);

    // Communication
    $commId = DB::table('communications')->insertGetId([
        'patient_id'         => $patId,
        'associate_id'       => $assocId,
        'type'               => 'Phone',
        'direction'          => 'Outbound',
        'subject'            => 'Initial contact',
        'summary'            => "Called {$fn} {$ln}. Explained VTA process and next steps. Patient is cooperative.",
        'communication_date' => ts($enqDay - 2, '10:00:00'),
        'created_by'         => $staffUserId,
        'created_at'         => ts($enqDay - 2),
        'updated_at'         => ts($enqDay - 2),
    ]);
    DB::table('activity_logs')->insert([
        'user_id'      => $staffUserId,
        'subject_type' => 'Communication',
        'subject_id'   => $commId,
        'patient_id'   => $patId,
        'associate_id' => $assocId,
        'action'       => 'logged',
        'description'  => "Initial outbound communication logged for {$name}",
        'metadata'     => json_encode(['direction' => 'Outbound', 'type' => 'Phone']),
        'occurred_at'  => ts($enqDay - 2, '10:05:00'),
    ]);

    // ── Assessment stage ──────────────────────────────────────────────────────
    if (!in_array($status, ['Response Sent','Awaiting LOI','Enquiry Logged'])) {
        $assDay = $enqDay - 6;

        DB::table('activity_logs')->insert([
            'user_id'      => $staffUserId,
            'subject_type' => 'Patient',
            'subject_id'   => $patId,
            'patient_id'   => $patId,
            'associate_id' => $assocId,
            'action'       => 'status_changed',
            'description'  => "Assessment scheduled for {$name} with {$assoc->name}",
            'metadata'     => json_encode(['from' => 'LOI Received', 'to' => 'Assessment Scheduled']),
            'occurred_at'  => ts($assDay, '09:00:00'),
        ]);

        $apptId = DB::table('appointments')->insertGetId([
            'patient_id'       => $patId,
            'associate_id'     => $assocId,
            'activity_type_id' => 1, // Assessment
            'scheduled_at'     => ts($assDay - 2, '10:00:00'),
            'duration_minutes' => 90,
            'location'         => 'Video Call',
            'status'           => 'Completed',
            'notes'            => "Initial assessment for {$name}",
            'created_at'       => ts($assDay),
            'updated_at'       => ts($assDay - 2),
        ]);
        DB::table('activity_logs')->insertOrIgnore([
            'user_id'      => $assocId,
            'subject_type' => 'Appointment',
            'subject_id'   => $apptId,
            'patient_id'   => $patId,
            'associate_id' => $assocId,
            'action'       => 'created',
            'description'  => "Assessment appointment booked for {$name}",
            'metadata'     => json_encode(['type' => 'Assessment']),
            'occurred_at'  => ts($assDay, '09:05:00'),
        ]);
        DB::table('activity_logs')->insert([
            'user_id'      => $assocId,
            'subject_type' => 'Appointment',
            'subject_id'   => $apptId,
            'patient_id'   => $patId,
            'associate_id' => $assocId,
            'action'       => 'status_changed',
            'description'  => "Assessment completed for {$name}",
            'metadata'     => json_encode(['from' => 'Scheduled', 'to' => 'Completed']),
            'occurred_at'  => ts($assDay - 2, '11:30:00'),
        ]);

        // Case note for assessment
        $cnId = DB::table('case_notes')->insertGetId([
            'patient_id'   => $patId,
            'associate_id' => $assocId,
            'appointment_id' => $apptId,
            'session_date' => tsDate($assDay - 2),
            'note_type'    => 'Session Note',
            'content'      => "Assessment completed. Patient presenting with musculoskeletal complaints related to workplace activity. Recommended occupational therapy intervention.",
            'is_signed_off'=> 1,
            'signed_off_by'=> $assocId,
            'signed_off_at'=> ts($assDay - 1),
            'created_at'   => ts($assDay - 2),
            'updated_at'   => ts($assDay - 1),
        ]);
        DB::table('activity_logs')->insert([
            ['user_id'=>$assocId,'subject_type'=>'CaseNote','subject_id'=>$cnId,'patient_id'=>$patId,'associate_id'=>$assocId,'action'=>'uploaded','description'=>"Assessment case note uploaded for {$name}",'metadata'=>json_encode(['note_type'=>'Session Note']),'occurred_at'=>ts($assDay-2,'12:00:00')],
            ['user_id'=>$assocId,'subject_type'=>'CaseNote','subject_id'=>$cnId,'patient_id'=>$patId,'associate_id'=>$assocId,'action'=>'signed_off','description'=>"Assessment note signed off for {$name}",'metadata'=>json_encode([]),'occurred_at'=>ts($assDay-1,'09:00:00')],
        ]);
    }

    // ── Funding + Treatment ───────────────────────────────────────────────────
    if (in_array($status, ['Treatment Active','Case Closed','Discharged','Funding Approved','Awaiting Funding Approval'])) {
        $fundDay       = $enqDay - 12;
        $approvedAmt   = rand(12, 30) * 500;
        $sessions      = rand(6, 15);
        $funder        = collect($funders)->random();
        $isActive      = !in_array($status, ['Case Closed','Discharged']);

        $fcId = DB::table('funding_cycles')->insertGetId([
            'patient_id'        => $patId,
            'funder_name'       => $funder,
            'cycle_number'      => 1,
            'approved_sessions' => $sessions,
            'approved_amount'   => $approvedAmt,
            'approval_date'     => tsDate($fundDay),
            'is_active'         => $isActive ? 1 : 0,
            'created_by'        => $staffUserId,
            'created_at'        => ts($fundDay),
            'updated_at'        => ts($fundDay),
        ]);
        DB::table('activity_logs')->insert([
            'user_id'      => $staffUserId,
            'subject_type' => 'FundingCycle',
            'subject_id'   => $fcId,
            'patient_id'   => $patId,
            'associate_id' => $assocId,
            'action'       => 'created',
            'description'  => "Funding approved for {$name} — £{$approvedAmt} ({$sessions} sessions) via {$funder}",
            'metadata'     => json_encode(['amount' => $approvedAmt, 'sessions' => $sessions, 'funder' => $funder]),
            'occurred_at'  => ts($fundDay, '10:00:00'),
        ]);
        DB::table('activity_logs')->insert([
            'user_id'      => $staffUserId,
            'subject_type' => 'Patient',
            'subject_id'   => $patId,
            'patient_id'   => $patId,
            'associate_id' => $assocId,
            'action'       => 'status_changed',
            'description'  => "Status updated to Funding Approved for {$name}",
            'metadata'     => json_encode(['from' => 'Awaiting Funding Approval', 'to' => 'Funding Approved']),
            'occurred_at'  => ts($fundDay, '10:05:00'),
        ]);

        // Sessions
        if (in_array($status, ['Treatment Active','Case Closed','Discharged'])) {
            $completed = in_array($status, ['Case Closed','Discharged']) ? $sessions : rand(2, max(2, $sessions - 1));

            for ($s = 1; $s <= $completed; $s++) {
                $sessDay = $fundDay - ($s * 7);
                if ($sessDay < 1) break;
                $isSigned = ($s < $completed) || in_array($status, ['Case Closed','Discharged']);

                $apptId = DB::table('appointments')->insertGetId([
                    'patient_id'       => $patId,
                    'associate_id'     => $assocId,
                    'activity_type_id' => 2, // Treatment
                    'scheduled_at'     => ts($sessDay, '10:00:00'),
                    'duration_minutes' => 60,
                    'location'         => collect($locations)->random(),
                    'status'           => 'Completed',
                    'notes'            => "Session {$s} for {$name}",
                    'created_at'       => ts($sessDay + 2),
                    'updated_at'       => ts($sessDay),
                ]);
                DB::table('activity_logs')->insert([
                    'user_id'      => $assocId,
                    'subject_type' => 'Appointment',
                    'subject_id'   => $apptId,
                    'patient_id'   => $patId,
                    'associate_id' => $assocId,
                    'action'       => 'status_changed',
                    'description'  => "Session {$s} completed for {$name}",
                    'metadata'     => json_encode(['session_number' => $s]),
                    'occurred_at'  => ts($sessDay, '11:00:00'),
                ]);

                $cnId = DB::table('case_notes')->insertGetId([
                    'patient_id'    => $patId,
                    'associate_id'  => $assocId,
                    'appointment_id'=> $apptId,
                    'session_date'  => tsDate($sessDay),
                    'note_type'     => collect($noteTypes)->random(),
                    'content'       => "Session {$s}: Patient showed " . collect(['good progress','some improvement','steady progress','marked improvement'])->random() . ". Continuing with planned programme.",
                    'is_signed_off' => $isSigned ? 1 : 0,
                    'signed_off_by' => $isSigned ? $assocId : null,
                    'signed_off_at' => $isSigned ? ts($sessDay - 1) : null,
                    'created_at'    => ts($sessDay),
                    'updated_at'    => ts($sessDay),
                ]);
                DB::table('activity_logs')->insert([
                    'user_id'      => $assocId,
                    'subject_type' => 'CaseNote',
                    'subject_id'   => $cnId,
                    'patient_id'   => $patId,
                    'associate_id' => $assocId,
                    'action'       => 'uploaded',
                    'description'  => "Case note uploaded for session {$s} — {$name}",
                    'metadata'     => json_encode(['session_number' => $s]),
                    'occurred_at'  => ts($sessDay, '12:00:00'),
                ]);
                if ($isSigned) {
                    DB::table('activity_logs')->insert([
                        'user_id'      => $assocId,
                        'subject_type' => 'CaseNote',
                        'subject_id'   => $cnId,
                        'patient_id'   => $patId,
                        'associate_id' => $assocId,
                        'action'       => 'signed_off',
                        'description'  => "Case note signed off for session {$s} — {$name}",
                        'metadata'     => json_encode(['session_number' => $s]),
                        'occurred_at'  => ts($sessDay - 1, '09:00:00'),
                    ]);
                }
            }

            // Associate invoice
            $invoiceAmt  = $completed * 120;
            $invStatus   = in_array($status, ['Case Closed','Discharged']) ? 'Paid' : collect(['Received','Verified'])->random();
            $invDaysAgo  = max(2, $fundDay - ($completed * 7) - 2);
            $invId = DB::table('associate_invoices')->insertGetId([
                'patient_id'         => $patId,
                'associate_id'       => $assocId,
                'funding_cycle_id'   => $fcId,
                'invoice_reference'  => 'AINV-' . str_pad($patId, 4, '0', STR_PAD_LEFT),
                'invoice_date'       => tsDate($invDaysAgo),
                'sessions_completed' => $completed,
                'total_amount'       => $invoiceAmt,
                'status'             => $invStatus,
                'payment_date'       => $invStatus === 'Paid' ? tsDate(max(1, $invDaysAgo - 7)) : null,
                'logged_by'          => $assocId,
                'created_at'         => ts($invDaysAgo),
                'updated_at'         => ts(1),
            ]);
            DB::table('activity_logs')->insert([
                'user_id'      => $assocId,
                'subject_type' => 'AssociateInvoice',
                'subject_id'   => $invId,
                'patient_id'   => $patId,
                'associate_id' => $assocId,
                'action'       => 'submitted',
                'description'  => "Associate invoice submitted for {$name} — £{$invoiceAmt}",
                'metadata'     => json_encode(['amount' => $invoiceAmt, 'sessions' => $completed]),
                'occurred_at'  => ts($invDaysAgo, '14:00:00'),
            ]);
            if ($invStatus === 'Paid') {
                DB::table('activity_logs')->insert([
                    'user_id'      => $adminUserId,
                    'subject_type' => 'AssociateInvoice',
                    'subject_id'   => $invId,
                    'patient_id'   => $patId,
                    'associate_id' => $assocId,
                    'action'       => 'paid',
                    'description'  => "Invoice paid to {$assoc->name} for {$name} — £{$invoiceAmt}",
                    'metadata'     => json_encode(['amount' => $invoiceAmt]),
                    'occurred_at'  => ts(max(1, $invDaysAgo - 7), '10:00:00'),
                ]);
            }

            // VTA invoice
            $vtaAmt = $invoiceAmt + rand(100, 300);
            $vtaInvId = DB::table('vta_invoices')->insertGetId([
                'patient_id'           => $patId,
                'funding_cycle_id'     => $fcId,
                'invoice_number'       => 'VTA-' . str_pad($patId, 4, '0', STR_PAD_LEFT) . '-001',
                'invoice_date'         => tsDate(max(1, $invDaysAgo - 1)),
                'recipient_type'       => 'Case Manager Company',
                'recipient_name'       => $employer,
                'sessions_invoiced'    => $completed,
                'total_amount'         => $vtaAmt,
                'status'               => $invStatus === 'Paid' ? 'Paid' : 'Sent',
                'payment_date'         => $invStatus === 'Paid' ? tsDate(max(1, $invDaysAgo - 14)) : null,
                'created_by'           => $adminUserId,
                'created_at'           => ts(max(1, $invDaysAgo - 1)),
                'updated_at'           => ts(1),
            ]);
            DB::table('activity_logs')->insert([
                'user_id'      => $adminUserId,
                'subject_type' => 'VtaInvoice',
                'subject_id'   => $vtaInvId,
                'patient_id'   => $patId,
                'associate_id' => $assocId,
                'action'       => 'created',
                'description'  => "VTA invoice raised for {$name} — £{$vtaAmt}",
                'metadata'     => json_encode(['amount' => $vtaAmt]),
                'occurred_at'  => ts(max(1, $invDaysAgo - 1), '15:00:00'),
            ]);
        }
    }

    // Case closed log
    if (in_array($status, ['Case Closed','Discharged'])) {
        DB::table('activity_logs')->insert([
            'user_id'      => $staffUserId,
            'subject_type' => 'Patient',
            'subject_id'   => $patId,
            'patient_id'   => $patId,
            'associate_id' => $assocId,
            'action'       => 'status_changed',
            'description'  => "{$name} — case {$status}",
            'metadata'     => json_encode(['from' => 'Treatment Active', 'to' => $status]),
            'occurred_at'  => ts(6, '09:00:00'),
        ]);
    }
}

// ─── 4. Future appointments for active patients ───────────────────────────────
echo "Adding future appointments...\n";
$activePatients = DB::table('patients')
    ->whereIn('status', ['Treatment Active','Funding Approved'])
    ->get(['id','first_name','last_name']);

foreach ($activePatients as $ap) {
    $pa = DB::table('patient_associates')->where('patient_id', $ap->id)->whereNull('end_date')->first();
    if (!$pa) continue;
    $numAppts = rand(1, 3);
    for ($f = 0; $f < $numAppts; $f++) {
        $daysAhead = rand(1, 14);
        $hour = rand(9, 16);
        $apptId = DB::table('appointments')->insertGetId([
            'patient_id'       => $ap->id,
            'associate_id'     => $pa->associate_id,
            'activity_type_id' => 2, // Treatment
            'scheduled_at'     => Carbon::now()->addDays($daysAhead)->setTime($hour, 0)->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'location'         => collect($locations)->random(),
            'status'           => 'Scheduled',
            'notes'            => "Upcoming treatment session for {$ap->first_name} {$ap->last_name}",
            'created_at'       => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'       => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('activity_logs')->insert([
            'user_id'      => $pa->associate_id,
            'subject_type' => 'Appointment',
            'subject_id'   => $apptId,
            'patient_id'   => $ap->id,
            'associate_id' => $pa->associate_id,
            'action'       => 'created',
            'description'  => "Upcoming appointment scheduled for {$ap->first_name} {$ap->last_name}",
            'metadata'     => json_encode(['days_ahead' => $daysAhead]),
            'occurred_at'  => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}

echo "\n=== Seed Complete ===\n";
echo "Activity logs: " . DB::table('activity_logs')->count() . "\n";
echo "Patients:      " . DB::table('patients')->count() . "\n";
echo "Appointments:  " . DB::table('appointments')->count() . "\n";
echo "Compliance:    " . DB::table('associate_compliance_documents')->count() . "\n";
echo "Enquiries:     " . DB::table('enquiries')->count() . "\n";
