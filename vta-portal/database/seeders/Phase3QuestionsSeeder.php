<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortalFeedbackItem;

class Phase3QuestionsSeeder extends Seeder
{
    public function run(): void
    {
        // Skip entire seeder if already seeded (first run)
        // For adding new questions to an existing run, check individually below

        $items = [

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q1',
                'priority'    => 'high',
                'title'       => 'Case Note Revision — How should Samy send feedback to an associate?',
                'description' => 'Currently when Samy reviews a case note he can only sign it off or leave it pending. There is no way to tell the associate what needs to change — it happens by phone or email outside the portal. We need to build a proper feedback loop. Three options: (A) Samy types a comment in the portal and the associate sees it flagged in their dashboard with the comment; (B) Just a "Send Back to Draft" button — no written comment, associate calls Samy for detail; (C) Samy types a comment and it is emailed to the associate automatically. Which approach works best for how you currently communicate with associates?',
                'dev_context' => 'Option A requires a case_note_feedback table (case_note_id, from_user_id, comment, created_at) and a new section on the associate portal patient page. Option B is a simple status reset. Option C requires mail configuration on EC2 plus a Notification class. Decision needed before any build starts.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q2',
                'priority'    => 'high',
                'title'       => '"On Hold" for patients — status or flag?',
                'description' => 'When a patient\'s treatment pauses (patient unavailable, funder dispute, waiting for re-authorisation) there is currently no way to mark that in the system. The patient stays as "Treatment Active" which is misleading, or you have to manually move the status backwards (which the system blocks). Two options: (A) Add "On Hold" as a proper status in the transition chain — Treatment Active can move to On Hold, and On Hold can move back to Treatment Active or forward to Awaiting Further Funding; (B) Add an "On Hold" flag alongside the current status so the status stays as Treatment Active but a yellow banner shows the patient is on hold. Do patients on hold still have appointments booked against them during the pause?',
                'dev_context' => 'Option A requires updating the allowedTransitions() array in PatientController and adding On Hold to all status dropdowns and report queries. Option B is a boolean column on patients with a UI flag. Important: the dashboard Daily Actions and Reports must handle On Hold patients correctly whichever option is chosen.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q3',
                'priority'    => 'medium',
                'title'       => 'Can a patient have two active Treatment associates simultaneously?',
                'description' => 'The system currently blocks allocating a second associate with the same role (e.g. two Treatment associates) to the same patient at the same time. This was built as a safeguard. However in practice a patient might need both a physiotherapist and a psychologist — both billed as Treatment. Does this ever happen at VTA? If yes, should we remove the one-per-role block entirely, or keep it but allow a specific override for certain role combinations?',
                'dev_context' => 'Current enforcement is in PatientController::addAssociate() — checks for existing active associate with same role. If removed, we need another way to identify the primary Treatment associate for invoicing purposes. If kept, the error message can be improved to be clearer.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q4',
                'priority'    => 'high',
                'title'       => 'Should Funders (insurers, solicitors) be a managed list or stay as free text?',
                'description' => 'Currently the payer/funder for each patient is entered as free text (name, email, address) directly on the patient record. This means: (1) if the same insurer funds 10 patients, their contact details are typed 10 times and may differ slightly each time; (2) there is no way to run a report showing "all patients funded by AXA" or "total outstanding from Aviva." Two options: (A) Create a Funders module — a managed list of insurers/solicitors/other funders that can be linked to patients, with all their contact details stored once; (B) Keep free text but add a funder type filter on the invoice reports. Do you regularly deal with the same funders across multiple patients, and do you need to see a consolidated view per funder?',
                'dev_context' => 'Option A requires a new funders table, FunderController, migration to link existing patients by matching free-text funder names, and updates to all invoice forms to use a funder lookup. Significant scope. Option B is a small filter addition to existing reports.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q5',
                'priority'    => 'medium',
                'title'       => 'Closing a case with unpaid invoices — block or warn?',
                'description' => 'When you move a patient to "Case Closed" should the system: (A) Block the status change if any VTA invoice is still unpaid — you must settle all invoices first; (B) Show a warning ("There are 2 unpaid VTA invoices totalling £1,400") but let you proceed if you confirm; (C) No check — you handle invoice reconciliation separately and do not want the system interfering. In practice, do cases sometimes close before all invoices are resolved (e.g. disputed invoice still ongoing)?',
                'dev_context' => 'Option A adds a check in PatientController::updateStatus() before allowing Case Closed transition. Option B adds a flash warning. Option C is no change. All are straightforward once the decision is made.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q6',
                'priority'    => 'high',
                'title'       => 'GDPR — How long must VTA retain clinical records?',
                'description' => 'UK guidance for independent healthcare providers typically requires retaining adult clinical records for 8 years from the date of last treatment. For patients who were children at the time of treatment, records must be kept until the patient turns 25 (or 26 if treatment ended before age 17). Does VTA follow the standard NHS/ICO retention schedule, or has your legal/GDPR adviser set a different retention period? This determines when the system can flag records as eligible for archiving or deletion. If you have a GDPR policy document or data retention schedule already, please share it.',
                'dev_context' => 'The retention period determines the logic for the archive flag and the GDPR anonymisation queue. Without this answer we cannot build the data retention module correctly.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q7',
                'priority'    => 'high',
                'title'       => 'GDPR — Delete or anonymise when a patient requests erasure?',
                'description' => 'Under GDPR Article 17, patients have the Right to Erasure. However, in a clinical setting, legitimate clinical and legal reasons usually override this right — meaning you do not have to delete clinical notes and assessment records, but you must be able to demonstrate why you are retaining them. The two approaches are: (A) Anonymise — replace personal identifiers (name, DOB, address, contact details) with "REDACTED" while keeping all clinical data intact for audit purposes; (B) Full delete — remove all records for the patient entirely. Has VTA received any legal or GDPR compliance advice on this? Do you have a process for handling Subject Access Requests (SARs) today?',
                'dev_context' => 'Anonymisation is the clinically safer option and is what most healthcare providers implement. Full deletion of clinical records carries legal risk. A GDPR Request Log table is needed regardless — to record who made the request, what was actioned, and when.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q8',
                'priority'    => 'medium',
                'title'       => 'Associate compliance documents — what needs tracking and what triggers an alert?',
                'description' => 'Associates working with patients will have documents that expire: DBS checks, professional indemnity insurance, professional registration (HCPC, BPS, BABCP etc.), and possibly a signed associate agreement with VTA. Questions: (1) Which of these documents does VTA currently track, and where (spreadsheet, email reminders)? (2) How many days before expiry should the system alert you — 30 days, 60 days, 90 days? (3) If a document expires and has not been renewed, should the system prevent that associate from being allocated to new patients, or just show a warning? (4) Should expired documents also be visible to the associate in their portal so they know to renew?',
                'dev_context' => 'Requires a new associate_documents table (associate_id, document_type, expiry_date, document_path, is_current) and an expiry check that runs on login or via a scheduled job. The "block allocation" option requires a check in PatientController::addAssociate().',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q9',
                'priority'    => 'medium',
                'title'       => 'Notifications — email alerts or dashboard-only?',
                'description' => 'Right now nothing sends you an email — you have to log in to see what has happened. Over the next 6–12 months as the case load grows, would you want email notifications for any of the following: (a) Associate uploads a case note and flags it for Clinical Head Review; (b) Associate submits an invoice; (c) A patient\'s funding cycle drops below 20% remaining balance; (d) An associate\'s compliance document is expiring in 30 days; (e) An enquiry has had no follow-up activity for 14 days. Please tick which ones you want as email, which as dashboard-only, and which not at all. Also — for associates, should they receive an email when Samy signs off their case note or sends it back for revision?',
                'dev_context' => 'Email notifications require configuring Laravel Mail with an SMTP provider (e.g. AWS SES or Mailgun) on the EC2 server. In-portal notifications are a simpler build using a notifications table. Decision on which triggers are needed determines the scope.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q10',
                'priority'    => 'medium',
                'title'       => 'Session count — should the system track sessions remaining automatically?',
                'description' => 'The funding cycle stores the approved number of sessions (e.g. 20 sessions approved). VTA Invoices store how many sessions were invoiced in each batch. Should the portal automatically calculate and display "sessions remaining = approved sessions minus total sessions invoiced so far"? And should it warn you when a patient is running low (e.g. 3 sessions left)? Currently the system tracks the financial balance (£ remaining) but not the session count separately. If sessions remaining reaches zero, should the system block raising further invoices, or just show a warning?',
                'dev_context' => 'The FundingBalanceService already handles the £ balance calculation. Adding session tracking follows the same pattern — sum(sessions_invoiced) from vta_invoices for the funding cycle. The "block on zero sessions" option requires a check in VtaInvoiceController::store().',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q11',
                'priority'    => 'low',
                'title'       => 'Appointments — should associates and case managers be able to book them?',
                'description' => 'Currently only admin and staff can book appointments in the portal. Associates can see their appointments in the associate portal but cannot create or modify them. Case managers can see their patients\' appointments (read-only) in the case manager portal. Two questions: (1) Would it be useful for associates to request or book appointments directly from their portal (subject to your confirmation/approval)? (2) Should case managers be able to request appointments for their patients from the case manager portal? Or is it better to keep all appointment booking centralised with you and your staff?',
                'dev_context' => 'Allowing associates to book appointments requires adding appointment create/edit routes to the associate portal with an "awaiting confirmation" status that Samy approves. Keeping it admin/staff only requires no change.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q12',
                'priority'    => 'high',
                'title'       => 'Companies — one creation path or two?',
                'description' => 'Currently companies can be created in two different places: (1) Directly via the Companies module (Settings or Companies menu), where you create a company and add case managers to it; (2) During enquiry conversion, where the system creates a company and case manager from the enquiry data automatically. After 12 months with 50–100 companies, having two creation paths risks duplicate company records (same company entered differently twice). Which should be the single authoritative path? Option A: Always use the Companies module first — when creating an enquiry, search and link to an existing company rather than typing the name free-form; Option B: Always create through the enquiry conversion flow — deprecate standalone company creation; Option C: Keep both paths but add a duplicate-detection step (search for matching company name before allowing a new one to be created). Which fits how you actually work day-to-day?',
                'dev_context' => 'Option A requires converting the company name field on enquiry create/edit to a searchable lookup with a "create new" fallback. Option B requires removing the standalone company resource. Option C requires a fuzzy name match on company create. This decision also affects how the Case Manager portal login creation flow works.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

        ];

        foreach ($items as $item) {
            PortalFeedbackItem::create($item);
        }
    }
}
