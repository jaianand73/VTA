<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortalFeedbackItem;

class PortalFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        if (PortalFeedbackItem::where('is_seeded', true)->exists()) {
            return;
        }

        $items = [

            // ── CHANGES ──────────────────────────────────────────────────────────
            ['type'=>'change','section'=>'A','reference'=>'A1','priority'=>'critical',
             'title'=>'"Qualified as Referral" gate — missing entirely',
             'description'=>'An enquiry currently jumps from "New" straight to "Converted" with no formal qualification step. Samy\'s Excel (cols R, S, T) shows a Qualified as Referral step must sit between follow-ups and patient creation.',
             'dev_context'=>'Add qualified_as_referral, qualified_date, qualified_remarks columns to enquiries table. New "Mark as Qualified" button on enquiries/show. "Create Patient" button only appears after qualification. Status flow: New → In Progress → Qualified → Converted → Not Proceeding.'],

            ['type'=>'change','section'=>'A','reference'=>'A2','priority'=>'critical',
             'title'=>'Link Enquiry → Patient (currently disconnected)',
             'description'=>'No enquiry_id on the patients table. EnquiryController::convert() creates a Case Manager then stops — no patient is ever created. Enquiry and patient are permanently disconnected records.',
             'dev_context'=>'Add enquiry_id nullable FK to patients. "Create Patient" button on qualified enquiry links to patients/create?enquiry_id=X. PatientController::create() pre-fills company, case manager, referral date, location from the enquiry.'],

            ['type'=>'change','section'=>'A','reference'=>'A3','priority'=>'medium',
             'title'=>'Multiple enquirer contacts with roles',
             'description'=>'Currently one enquirer_name field. Samy\'s PDF and Excel show two distinct people at enquiry stage: Case Manager Name and Lead Professional.',
             'dev_context'=>'New table enquiry_contacts (enquiry_id, name, role, email, phone). Roles: Case Manager / Lead Professional / Other. Dynamic ADD ANOTHER rows on create/edit forms.'],

            ['type'=>'change','section'=>'A','reference'=>'A4','priority'=>'medium',
             'title'=>'Four follow-up slots on Enquiry',
             'description'=>'Samy\'s Excel has four follow-up date columns (N–Q), each with date + remarks. Currently only one follow-up date field exists.',
             'dev_context'=>'Existing communications table (linked by enquiry_id) can serve follow-ups. Restructure enquiries/show.blade.php to show them as numbered follow-ups. Support at least 4.'],

            ['type'=>'change','section'=>'B','reference'=>'B1','priority'=>'low',
             'title'=>'Next of Kin fields on Patient',
             'description'=>'Samy\'s PDF Page 2 shows Next of Kin — Name, Email, Phone on every patient. Currently absent from the schema.',
             'dev_context'=>'Add nok_name, nok_email, nok_phone to patients table. Update Patient::$fillable and patient create/edit/show views.'],

            ['type'=>'change','section'=>'B','reference'=>'B2','priority'=>'medium',
             'title'=>'Referrer details — four named roles on Patient',
             'description'=>'Samy\'s Excel shows Case Manager / Deputy / Solicitors / Insurer as four separately named contacts at the patient level, not a single role dropdown.',
             'dev_context'=>'New table patient_referrers (patient_id, name, role, address, email, phone). New PatientReferrer model + hasMany on Patient. Dynamic rows on patient forms.'],

            ['type'=>'change','section'=>'B','reference'=>'B3','priority'=>'medium',
             'title'=>'Auto status transitions on Patient',
             'description'=>'All 15 patient statuses are manually set with no enforcement or automation. Statuses should auto-transition based on system events.',
             'dev_context'=>'Triggers: assessment created → Assessment Scheduled, report marked sent → Report Sent, cost estimation created → Cost Estimation Sent, funding cycle + doc → Funding Approved, first invoice sent → Treatment Active, discharge date set → Discharged.'],

            ['type'=>'change','section'=>'C','reference'=>'C1','priority'=>'new',
             'title'=>'Assessment module — new table, model and controller',
             'description'=>'A formal Assessment stage exists between referral qualification and cost estimation in Samy\'s process (PDF Page 2, Excel cols Z–AD) but there is no table, model, controller or UI for it in the portal.',
             'dev_context'=>'New table assessments: fee_agreed_amount + fee_agreed_document_path, date_client_contacted, assessor, venue, assessment_date, assessment_cost + assessment_cost_document_path, report_sent (bool) + report_document_path, special_instructions, notes. New AssessmentController. New routes. New section on patients/show.blade.php.'],

            ['type'=>'change','section'=>'C','reference'=>'C2','priority'=>'new',
             'title'=>'Cost for Assessment vs Cost Estimation — two separate figures',
             'description'=>'PDF shows two distinct boxed figures: "Cost for Assessment £--" and "Cost Estimation £--" — each with a document attachment. These are separate financial events.',
             'dev_context'=>'Assessment cost lives on the new assessments table. Cost Estimation remains as a separate cost_estimations record. Both figures must surface in the Accounts views.'],

            ['type'=>'change','section'=>'D','reference'=>'D1','priority'=>'critical',
             'title'=>'Document upload on Funding Cycle form (Gap B)',
             'description'=>'The funding-cycles/create.blade.php has no file input for approval_document_path. This makes BR-C1 permanently unsatisfiable via the UI — no patient can ever reach "Funding Approved" status through the normal flow.',
             'dev_context'=>'Add file upload input to funding-cycles/create and edit views. Handle storage in FundingCycleController::store() and update() using the vta-documents private disk.'],

            ['type'=>'change','section'=>'D','reference'=>'D2','priority'=>'medium',
             'title'=>'Show all funding cycles on patient page (Gap C)',
             'description'=>'patients/show.blade.php uses $patient->fundingCycles->first() — hard-coded to show only the first cycle. Multi-phase patients lose visibility of all subsequent cycles.',
             'dev_context'=>'Replace ->first() with a loop over all cycles. Show cycle number, approved amount, funder name, approval date and remaining balance per cycle.'],

            ['type'=>'change','section'=>'D','reference'=>'D3','priority'=>'medium',
             'title'=>'"Add Funding Cycle" button on patient page (Gap A)',
             'description'=>'No button exists on the patient page to add a funding cycle. Users must navigate away to Funding Cycles → Create and manually re-select the patient.',
             'dev_context'=>'Add button linking to funding-cycles/create?patient_id=X. Filter cost estimation dropdown on the funding cycle form to the selected patient (Gap D).'],

            ['type'=>'change','section'=>'E','reference'=>'E1','priority'=>'low',
             'title'=>'Rename Finance → Accounts in navigation',
             'description'=>'Samy uses "Accounts" terminology throughout all documents — PDFs, Excel, PPTX. The nav currently says "Finance".',
             'dev_context'=>'One label change in the navigation partial (layouts/app.blade.php). URL paths /finance/... can remain unchanged.'],

            ['type'=>'change','section'=>'E','reference'=>'E2','priority'=>'medium',
             'title'=>'Associate invoice rate card auto-calculation (Gap E)',
             'description'=>'Staff currently enter all invoice amounts manually. No auto-calculation from the associate\'s rate card.',
             'dev_context'=>'Add rate/session fields to associates table. AssociateInvoiceController auto-calculates amount from rate × sessions. Show calculated value with ability to override.'],

            ['type'=>'change','section'=>'E','reference'=>'E3','priority'=>'medium',
             'title'=>'Filter associate invoice patient dropdown (Gap F)',
             'description'=>'All patients are shown in the associate invoice patient dropdown regardless of which associate is selected.',
             'dev_context'=>'Filter patient dropdown to patients assigned to the selected associate via JavaScript on the associate invoice form.'],

            ['type'=>'change','section'=>'F','reference'=>'F1','priority'=>'medium',
             'title'=>'Dashboard — 5 widgets from PPTX',
             'description'=>'PPTX Slide 2 confirms Samy expects 5 dashboard widgets: Emails (unprocessed intake logs), Clinical Head Review (needs_review case notes), Pending Enquiries/Referrals, Invoices/Payments Due, Shared Calendar.',
             'dev_context'=>'Update DashboardController to pass 5 data counts. Dashboard view: 5 widget cards. Shared calendar already partially exists.'],

            ['type'=>'change','section'=>'G','reference'=>'G1','priority'=>'low',
             'title'=>'Promote Email Intake to top-level nav section',
             'description'=>'PPTX Slide 1 lists "Emails" as a top-level portal section. Currently email intake is buried under Admin in the nav.',
             'dev_context'=>'Move Email Intake link to its own nav section above Admin. Consider a badge showing unprocessed count.'],

            ['type'=>'change','section'=>'G','reference'=>'G2','priority'=>'medium',
             'title'=>'Reports section — 4 basic reports',
             'description'=>'PPTX Slide 1 lists "Reports" as a top-level portal section. No reports section exists at all currently.',
             'dev_context'=>'Build 4 pre-written query reports: Active patients by status, Funding balance summary, Invoice ageing, Associate activity. No complex engine needed — simple controller + view per report.'],

            // ── QUESTIONS ─────────────────────────────────────────────────────────
            ['type'=>'question','section'=>'Enquiry','reference'=>'Q1','priority'=>'high',
             'title'=>'What makes an enquiry "Qualified as a Referral"?',
             'description'=>'Is this a formal decision with specific criteria (e.g., funding confirmed, client willing to proceed) or your judgement call? Does anything need to be recorded at that point beyond a date and remarks?'],

            ['type'=>'question','section'=>'Enquiry','reference'=>'Q2','priority'=>'medium',
             'title'=>'Who can perform the qualification step?',
             'description'=>'Is it always you, or can any staff member mark an enquiry as qualified?'],

            ['type'=>'question','section'=>'Enquiry','reference'=>'Q3','priority'=>'high',
             'title'=>'Case Manager Name vs Lead Professional — what is the difference?',
             'description'=>'Your Excel has Case Manager Name (col F) and Lead Professional (col G) as two separate people at enquiry stage. Are both always present? What is the practical difference between them in VTA\'s process?'],

            ['type'=>'question','section'=>'Enquiry','reference'=>'Q4','priority'=>'medium',
             'title'=>'Can one enquiry involve multiple companies?',
             'description'=>'For example, a solicitor firm AND an insurance company on the same enquiry. Or is it always one company per enquiry?'],

            ['type'=>'question','section'=>'Enquiry','reference'=>'Q5','priority'=>'medium',
             'title'=>'What was meant to go in Step 2 of the Excel?',
             'description'=>'The Process mapping.xlsx has a "Step 2" sheet that is completely blank. Was this intended to cover treatment tracking, invoicing, a second phase, or something else?'],

            ['type'=>'question','section'=>'Assessment','reference'=>'Q6','priority'=>'critical',
             'title'=>'Is there always one assessment per patient, or can there be multiple?',
             'description'=>'For example, a re-assessment after treatment. This determines whether the new assessments table is one-to-one or one-to-many with patients — a fundamental design decision.'],

            ['type'=>'question','section'=>'Assessment','reference'=>'Q7','priority'=>'high',
             'title'=>'Who is the Clinician for Initial Assessment?',
             'description'=>'Is it always you, or can it be one of the associates? This affects whether the assessor field links to the associates table or is a free-text name field.'],

            ['type'=>'question','section'=>'Assessment','reference'=>'Q8','priority'=>'high',
             'title'=>'Fee for Initial Assessment — who pays, and is it invoiced?',
             'description'=>'"Fee for Initial Assessment agreed with Referrer" (Excel col Z) — who pays this: the case manager company, solicitor, or insurer? Is it invoiced separately via the VTA invoice system, or tracked for reference only?'],

            ['type'=>'question','section'=>'Assessment','reference'=>'Q9','priority'=>'medium',
             'title'=>'Assessment Report Sent — to whom and how?',
             'description'=>'When the assessment report is marked as sent, who receives it — the referrer (case manager/solicitor), the insurer, or all parties? Is it sent by email through the portal or externally?'],

            ['type'=>'question','section'=>'Funding','reference'=>'Q10','priority'=>'critical',
             'title'=>'Should Cost Estimation and Funding Cycle remain separate or merge?',
             'description'=>'Currently they are two separate records. Your Excel treats them as columns on the same row. If merged, what event triggers a cost estimation becoming "approved"? This is a significant data model decision.'],

            ['type'=>'question','section'=>'Funding','reference'=>'Q11','priority'=>'medium',
             'title'=>'Who is the "funder"?',
             'description'=>'Is it always the insurer, or can it be a solicitor firm or the case management company paying directly? This affects what the funder name and funder reference fields mean in practice.'],

            ['type'=>'question','section'=>'Funding','reference'=>'Q12','priority'=>'medium',
             'title'=>'Do you verify associate invoices against the cost estimation?',
             'description'=>'When an associate invoice arrives, do you check the hours/sessions against the original cost estimation before approving payment? If yes, the portal should surface the estimation for comparison.'],

            ['type'=>'question','section'=>'Funding','reference'=>'Q13','priority'=>'medium',
             'title'=>'Can a patient have multiple funding cycles running simultaneously?',
             'description'=>'For example, different funders covering different treatments at the same time. Or is funding always sequential — one cycle ends before the next begins?'],

            ['type'=>'question','section'=>'Dashboard','reference'=>'Q14','priority'=>'high',
             'title'=>'Clinical Head Review — who is the Clinical Head and what triggers review?',
             'description'=>'Who is the Clinical Head in VTA? What criteria flag a case note for review — is it a manual flag set by staff, or automatically triggered (e.g., after a certain number of sessions, or when a patient reaches a specific status)?'],

            ['type'=>'question','section'=>'Dashboard','reference'=>'Q15','priority'=>'high',
             'title'=>'Which reports are most urgent?',
             'description'=>'Suggested options: Active patients by status, Monthly invoice summary, Funding balance per patient, Associate activity report, Enquiry conversion rate. Which of these do you need first?'],

            ['type'=>'question','section'=>'Dashboard','reference'=>'Q16','priority'=>'medium',
             'title'=>'"Emails" in the nav — read/reply inside portal or just auto-log?',
             'description'=>'Do you want to read and reply to emails inside the portal, or just have incoming emails automatically logged against patient/enquiry records for reference?'],

            ['type'=>'question','section'=>'Associates','reference'=>'Q17','priority'=>'medium',
             'title'=>'"Nearest Associate" on enquiry — auto-allocated or just a suggestion?',
             'description'=>'Does "Nearest Associate" (Excel col L) automatically become the associate allocated to the patient once created, or is it just a reference suggestion at enquiry stage?'],

            ['type'=>'question','section'=>'Associates','reference'=>'Q18','priority'=>'medium',
             'title'=>'Can one patient have multiple associates simultaneously?',
             'description'=>'For example, a physiotherapist and a rehabilitation assistant working with the same patient at the same time. The patient_associates table supports this but we need to confirm this is the intended behaviour.'],

            // ── IMPROVEMENTS ──────────────────────────────────────────────────────
            ['type'=>'improvement','section'=>'Workflow','reference'=>'I1','priority'=>'high',
             'title'=>'Enforce a linear status progression with auto-transitions',
             'description'=>'The current 15-step patient status enum is manually set with no enforcement. Make transitions automatic on key triggers and block backwards jumps. Prevents staff from skipping steps.',
             'dev_context'=>'Add status transition logic in FundingCycleController::store(), AssessmentController::store(), VtaInvoiceController::updateStatus() etc. Each trigger auto-calls $patient->update([status => ...]).'],

            ['type'=>'improvement','section'=>'Workflow','reference'=>'I2','priority'=>'high',
             'title'=>'Pre-fill patient form from enquiry data',
             'description'=>'When "Create Patient" is clicked from a qualified enquiry, auto-populate company, case manager, referral date, client location, and referrer contacts. Eliminates the main double-data-entry pain point.',
             'dev_context'=>'PatientController::create() receives enquiry_id query param, loads the enquiry, passes it to the view. Blade form uses old() with enquiry data as fallback values.'],

            ['type'=>'improvement','section'=>'UX','reference'=>'I3','priority'=>'medium',
             'title'=>'Single "Patient Journey" timeline view',
             'description'=>'Add a chronological activity feed on the patient page showing all events in one scrollable view: enquiry → qualified → patient created → communications → assessment → cost estimation → funding → associate allocated → invoices → case notes → discharge.',
             'dev_context'=>'Fetch all related events (communications, documents, case notes, invoices, status changes) ordered by date. Render as a timeline component on patients/show.blade.php.'],

            ['type'=>'improvement','section'=>'UX','reference'=>'I4','priority'=>'medium',
             'title'=>'"Quick Actions" panel — show only the next logical step',
             'description'=>'A context-aware panel on the patient page surfacing only the next action based on current status. Prevents staff from skipping steps and makes the workflow self-documenting.',
             'dev_context'=>'Map patient status to a next action: Response Sent → Schedule Assessment, Report Sent → Create Cost Estimation, etc. Render a single prominent CTA button on patients/show.blade.php based on status.'],

            ['type'=>'improvement','section'=>'Compliance','reference'=>'I5','priority'=>'high',
             'title'=>'Document enforcement at three critical gates',
             'description'=>'Block progression without a document at: (1) Funding Approved without approval_document_path — BR-C1. (2) VTA Invoice "Sent" without document — BR-F6. (3) Assessment "Report Sent" without the report uploaded.',
             'dev_context'=>'FundingCycleController::store(): if no document, block status change. VtaInvoiceController::updateStatus() already has BR-F6 partially. Add equivalent check on new AssessmentController.'],

            ['type'=>'improvement','section'=>'Finance','reference'=>'I6','priority'=>'medium',
             'title'=>'Funding balance live on the patient page',
             'description'=>'FundingBalanceService already computes the remaining balance — it just needs surfacing on the patient page. Show approved amount, invoiced to date, remaining balance and a progress bar.',
             'dev_context'=>'PatientController::show() already loads fundingCycles. Inject FundingBalanceService and pass balance data to the view. Render a small funding summary card on patients/show.blade.php.'],

            ['type'=>'improvement','section'=>'Workflow','reference'=>'I7','priority'=>'low',
             'title'=>'Associate allocation with automatic activity log',
             'description'=>'When an associate is allocated to a patient, auto-create a communication log entry ("Associate [name] allocated on [date]") and show it on the patient timeline.',
             'dev_context'=>'In PatientController::addAssociate(), after saving PatientAssociate, call Communication::create() with type=\'Note\' and a generated summary. Sets up for future email notification.'],

            ['type'=>'improvement','section'=>'Finance','reference'=>'I8','priority'=>'medium',
             'title'=>'Email intake — tag emails to records',
             'description'=>'The email_intake_logs table exists but emails are untagged. Allow each email to be tagged to a patient, enquiry, VTA invoice or funding cycle so the full email trail is visible from each record.',
             'dev_context'=>'Add patient_id, enquiry_id, vta_invoice_id, funding_cycle_id nullable FKs to email_intake_logs. Update EmailIntakeController::link() to accept any of these. Surface tagged emails on patients/show and enquiries/show.'],

            ['type'=>'improvement','section'=>'Reports','reference'=>'I9','priority'=>'medium',
             'title'=>'Reports section — 4 basic reports to start',
             'description'=>'Build four pre-written query reports as a starting point: Active patients by status, Funding balance summary, Invoice ageing, Associate activity by date range.',
             'dev_context'=>'New ReportsController with 4 methods. Each returns a simple view with a query result. Add to the existing finance.reports route or create new /reports/* routes under admin middleware.'],

            ['type'=>'improvement','section'=>'Docs','reference'=>'I10','priority'=>'low',
             'title'=>'Correct CLAUDE.md project path and update architecture docs',
             'description'=>'CLAUDE.md references C:\\xampp\\htdocs\\vta-portal but the actual project is at C:\\xampp\\htdocs\\VTA_NEW\\vta-portal. Also needs to document: enquiry_id FK on patients, new Assessment table, Qualified as Referral status, 5 dashboard widgets.',
             'dev_context'=>'Update CLAUDE.md: fix path, add Assessment to architecture section, add enquiry_id to Patient model notes, update Next Steps list to reflect completed and new items.'],
        ];

        foreach ($items as $item) {
            PortalFeedbackItem::create(array_merge($item, ['is_seeded' => true]));
        }
    }
}
