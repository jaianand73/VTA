# VTA Portal — Full Analysis & Action Plan
**Session date:** 27 June 2026  
**Sources reviewed:** Handwritten PDFs (x2), Actions from Meeting 25th June.pptx, Process mapping.xlsx, Live code review

---

## PART 1 — CHANGES TO BE MADE

### Section A — Enquiry Module

#### A1. "Qualified as Referral" gate (CRITICAL — missing entirely)
Currently the Enquiry skips straight from "New" to "Converted" (Case Manager creation). Samy's Excel (columns R, S, T) shows a formal **Qualified as Referral** step must sit between follow-ups and patient creation.

**Changes needed:**
- Add `qualified_as_referral` (boolean), `qualified_date` (date), `qualified_remarks` (text) to `enquiries` table
- New migration for these columns
- New "Mark as Qualified Referral" action button on `enquiries/show.blade.php`
- Only after qualification does the "Create Patient" button appear
- Enquiry status flow becomes: `New → In Progress → Qualified → Converted → Not Proceeding`

#### A2. Multiple enquirer contacts with roles
Currently one `enquirer_name` field. Samy's PDF and Excel show two named people at enquiry stage: **Case Manager Name** and **Lead Professional** — these are distinct contacts.

**Changes needed:**
- New table `enquiry_contacts` (enquiry_id, name, role [Case Manager / Lead Professional / Other], email, phone)
- New `EnquiryContact` model with `hasMany` on `Enquiry`
- Enquiry create/edit forms: dynamic "ADD ANOTHER" rows (JavaScript)
- Enquiry show: list all contacts

#### A3. Four follow-up slots (not one)
Samy's Excel has Follow-up 1, 2, 3, 4 (columns N–Q) each with a date. The PDF confirms date + remarks per follow-up.

**Changes needed:**
- The existing `communications` table (linked by `enquiry_id`) can serve as follow-ups
- Restructure `enquiries/show.blade.php` follow-up section to show communications ordered as numbered follow-ups
- Ensure the Communications form on enquiry clearly supports "Follow-up" type with date + remarks
- Support at least 4 follow-ups visually

#### A4. Link Enquiry → Patient (currently disconnected)
`EnquiryController::convert()` creates a Case Manager but never creates a Patient. There is no `enquiry_id` on the `patients` table. Once converted, the two records are permanently disconnected.

**Changes needed:**
- Add `enquiry_id` (nullable FK) to `patients` table via new migration
- After qualification, show "Create Patient" button on `enquiries/show.blade.php` linking to `patients/create?enquiry_id=X`
- `PatientController::create()` pre-fills from the enquiry (name of company, case manager, referral date, location)
- `PatientController::store()` saves `enquiry_id` on the new patient

---

### Section B — Patient Module

#### B1. Next of Kin fields
Samy's PDF Page 2 shows: Next of Kin — Name, Email, Phone on every patient.

**Changes needed:**
- Add `nok_name`, `nok_email`, `nok_phone` columns to `patients` table
- Update `Patient::$fillable`
- Add fields to `patients/create.blade.php`, `patients/edit.blade.php`, `patients/show.blade.php`

#### B2. Referrer details — four named roles
Samy's PDF and Excel show the patient's referrer section needs: **Case Manager / Deputy / Solicitors / Insurer** as four separately named contacts (not one role dropdown).

**Changes needed:**
- New table `patient_referrers` (patient_id, name, role [Case Manager / Deputy / Solicitor / Insurer / Other], address, email, phone)
- New `PatientReferrer` model + `hasMany` on `Patient`
- Dynamic "ADD ANOTHER" referrer rows on patient create/edit forms
- Patient show: referrer section listing all contacts

#### B3. Special Instructions field
Mentioned in both the PDF (on patient and referrer sections) and implied in the Excel.

**Changes needed:**
- Add `special_instructions` (text, nullable) to `patients` table
- Show on patient create/edit/show

#### B4. Patient status — "Approved for Treatment" clarity
Currently patient has a 15-step manual status enum. Samy's Excel column AE is simply "Approved for treatment: Yes/No". The current portal has no enforcement — status can be freely changed regardless of whether funding is in place.

**Changes needed:**
- BR-C1 enforcement: block patient status reaching "Funding Approved" if no `approval_document_path` on the funding cycle
- Auto-transition patient status to "Funding Approved" when a Funding Cycle with document is created (instead of manual edit)

---

### Section C — Assessment Module (NEW — does not exist)

Samy's PDF Page 2 and his Excel (columns Z–AD) confirm there is a formal **Assessment** stage between referral qualification and cost estimation. This is a first-class entity the portal currently has no table or UI for.

#### C1. New Assessment table
**Fields needed:**
- `patient_id` (FK)
- `fee_agreed_amount` (decimal) + `fee_agreed_document_path` — fee agreed with referrer before assessment
- `date_client_contacted` (date)
- `assessor` (string) — clinician for initial assessment
- `venue` (string)
- `assessment_date` (date)
- `assessment_cost` (decimal) + `assessment_cost_document_path` — actual cost of assessment
- `report_sent` (boolean Yes/No) + `report_document_path`
- `special_instructions` (text)
- `notes` (text)
- `created_by` (FK → users)

**Changes needed:**
- New migration for `assessments` table
- New `Assessment` model + `hasOne/hasMany` on `Patient`
- New `AssessmentController` (store, update, show)
- New assessment section on `patients/show.blade.php` OR dedicated assessment views
- New routes: `assessments.*`
- All document fields use the existing `vta-documents` private disk

#### C2. Cost for Assessment vs Cost Estimation — separate figures
Currently `cost_estimations` only holds one `estimated_amount`. Samy's PDF shows "Cost for Assessment £--" and "Cost Estimation £--" as two distinct boxed figures, both with document attachments. Assessment cost lives on the Assessment record (above); Cost Estimation remains as a separate `cost_estimations` record.

**Changes needed:**
- Assessment cost fields live on the new `assessments` table (C1 above)
- `cost_estimations` table gets a `cost_estimation_document_path` column (it currently has `document_path` — confirm this is sufficient)
- PDF note: "Figures linked to Accounts section" — ensure both figures feed into the finance/accounts views

---

### Section D — Funding Cycle Module

#### D1. Document upload on Funding Cycle create form (Gap B — CRITICAL)
The `funding-cycles/create.blade.php` has no file input for `approval_document_path`. This makes BR-C1 permanently unsatisfiable via the UI.

**Changes needed:**
- Add file upload input to `funding-cycles/create.blade.php` and `edit.blade.php`
- Handle file storage in `FundingCycleController::store()` and `update()` using `vta-documents` disk

#### D2. Show all funding cycles on patient page (Gap C)
`patients/show.blade.php` uses `$patient->fundingCycles->first()` — hard-coded to only the first cycle.

**Changes needed:**
- Replace with `->all()` or `->get()` and loop all cycles
- Show cycle number, approved amount, funder, approval date, remaining balance per cycle

#### D3. "Add Funding Cycle" button on patient page (Gap A)
No button exists on the patient page to add a funding cycle. Users must navigate away.

**Changes needed:**
- Add "Add Funding Cycle" button on `patients/show.blade.php` linking to `funding-cycles/create?patient_id=X`
- Filter cost estimation dropdown on funding cycle form to the selected patient (Gap D)

#### D4. Auto-update patient status on Funding Cycle creation
Currently patient status never changes automatically. Creating a funding cycle with an approval document should auto-set patient status to "Funding Approved".

**Changes needed:**
- In `FundingCycleController::store()`, if `approval_document_path` is set, update `patient->status = 'Funding Approved'`

---

### Section E — Finance / Accounts Module

#### E1. Rename "Finance" → "Accounts" in navigation
Samy uses "Accounts" terminology throughout. Confirmed by the PDF note "Figures linked to Accounts section" and the PPTX.

**Changes needed:**
- Update navigation partial label only (1 file, 1 line)
- URL paths (`/finance/...`) can remain unchanged

#### E2. Associate Invoice — rate card auto-calculation (Gap E)
Staff currently enter all amounts manually. No auto-calculation from associate rate card.

**Changes needed:**
- Add rate card fields to `associates` table (hourly/session rate)
- `AssociateInvoiceController::store()` auto-calculates amount from rate × sessions
- Show calculated amount in the invoice form with ability to override

#### E3. Associate Invoice — patient dropdown filtered by associate (Gap F)
All patients shown regardless of which associate is selected.

**Changes needed:**
- Filter patient dropdown to patients assigned to the selected associate (JavaScript or Ajax)

---

### Section F — Dashboard (from PPTX Slide 2)

The PPTX confirms Samy wants 5 specific dashboard widgets:

| Widget | What it needs |
|--------|--------------|
| **Emails** | Unread/unprocessed email intake logs count |
| **Clinical Head Review** | Count of case notes flagged `needs_review = true` |
| **Pending Enquiries/Referrals** | Count of enquiries in `New` or `In Progress` status |
| **Invoices/Payments Due** | Count/total of VTA invoices in `Sent` or `Overdue` status |
| **Shared Calendar** | Existing calendar — confirm it's visible on dashboard |

**Changes needed:**
- `DashboardController` to pass 5 data points
- Dashboard view: 5 widget cards

---

### Section G — Navigation (from PPTX Slide 1)

Samy's PPTX lists these as expected top-level portal sections:
- Companies data ✓ (exists)
- Associates Resource ✓ (exists)
- Emails — email intake exists but not as a top-level nav item
- Reports — **does not exist at all**
- Core processes — maps to Patients / Enquiries / Funding
- Supportive Processes — maps to Settings / Users / Documents

**Changes needed:**
- Move email intake to top-level nav
- Add a basic Reports section (even if initially just pre-built queries: active patients by status, invoices by period, funding balance per patient)

---

## PART 2 — QUESTIONS TO ASK SAMY

### About the Enquiry → Referral flow

1. **What exactly makes an enquiry "Qualified as a Referral"?** Is it a formal criteria (e.g., funding confirmed, client willing to proceed) or just Samy's judgement call? Does anything need to be recorded at that point beyond a date and remarks?

2. **Who performs the qualification step?** Is it always Samy, or can any staff member mark an enquiry as qualified?

3. **The Excel has Case Manager Name (col F) and Lead Professional (col G) as two separate people at enquiry stage.** Are these always both present, or is it common to have just one? What's the practical difference between them in VTA's process?

4. **Can one enquiry involve multiple companies** (e.g., a solicitor firm AND an insurance company)? Or is it always one company per enquiry?

5. **The Excel's "Step 2" sheet is blank.** What was meant to go there — a second phase of the process? Treatment tracking? Invoicing?

### About the Assessment

6. **Is there always one assessment per patient, or can a patient have multiple assessments** (e.g., re-assessment after treatment)? The portal needs to know whether `assessments` is one-to-one or one-to-many with patients.

7. **Who is the "clinician for initial assessment" always Samy, or can it be an associate?** This affects whether the assessor field links to the associates table or is just a free-text name.

8. **"Fee for Initial Ax agreed with Referrer" (Excel col Z) — who pays this, and does it get invoiced separately via the VTA invoice system?** Or is it tracked only for reference?

9. **"Assessment Report Sent Yes/No" — sent to whom?** The referrer (case manager/solicitor), the insurer, or all parties? Does VTA send it by email through the portal or externally?

### About Funding & Accounts

10. **Should Cost Estimation and Funding Cycle remain as two separate records, or merge into one?** (Currently they are separate. Your Excel treats them as columns on the same row.) If merged, what's the trigger for a cost estimation becoming "approved"?

11. **What is the "funder" — is it always the insurer, or can it be a solicitor firm or the case management company directly?** This affects what the funder name/reference fields mean.

12. **When an associate invoice arrives, does Samy verify the hours/sessions against the cost estimation before approving it?** If yes, the portal should surface the original estimation for comparison.

13. **Are there scenarios where a patient has multiple funding cycles running simultaneously** (e.g., different funders for different treatments)? Or is it always sequential?

### About the Dashboard & Reports

14. **"Clinical Head Review" widget — who is the Clinical Head?** Is it always Samy? What criteria flag a case note for review — is it a manual flag, or automatically triggered (e.g., after X sessions, or when a patient reaches a certain status)?

15. **What reports does Samy actually need?** Suggestions to confirm: Active patients by status, Monthly invoice summary, Funding balance per patient, Associate activity report, Enquiry conversion rate. Which of these are most urgent?

16. **"Emails" in the PPTX nav — does Samy want to read and reply to emails inside the portal, or just have incoming emails auto-logged against patient/enquiry records?**

### About the Associate allocation

17. **"Nearest Associate" on the enquiry (Excel col L) — is this automatically the associate who gets allocated to the patient, or just a suggestion?** Does the allocation always happen at the enquiry stage, or later after assessment?

18. **Can one patient have multiple associates** (e.g., a physiotherapist and a rehabilitation assistant simultaneously)? The `patient_associates` table supports this but we want to confirm.

---

## PART 3 — SUGGESTED IMPROVEMENTS FOR SMOOTH END-TO-END FLOW

### 3.1 Enforce a Linear Status Progression

The current 15-step patient status enum is manually set with no enforcement. **Recommend: make status transitions automatic wherever possible** and block backwards jumps.

| Trigger | Auto status change |
|---------|-------------------|
| Enquiry marked "Qualified" | Enquiry status → `Qualified` |
| Patient created from enquiry | Patient status → `Enquiry Logged` |
| First communication/response logged | Patient status → `Response Sent` |
| Assessment record created | Patient status → `Assessment Scheduled` |
| Assessment report marked sent | Patient status → `Report Sent` |
| Cost estimation created | Patient status → `Cost Estimation Sent` |
| Funding cycle created (no document yet) | Patient status → `Awaiting Funding Approval` |
| Funding cycle created WITH document | Patient status → `Funding Approved` |
| First VTA invoice sent | Patient status → `Treatment Active` |
| Discharge date set | Patient status → `Discharged` |

### 3.2 Pre-fill Patient from Enquiry

When "Create Patient" is clicked from a qualified enquiry, pre-populate:
- Company → referrer company
- Case manager → converted case manager
- Referral date → enquiry qualified date
- Location → client's location from enquiry
- Referrer contacts → from enquiry contacts (Case Manager, Lead Professional)

This eliminates double data entry, which is the main pain point in the current process.

### 3.3 Single "Patient Journey" Timeline View

On `patients/show.blade.php`, add a **chronological activity feed** showing all events in one scrollable view:
- Enquiry created → Qualified → Patient created
- Communications / follow-ups
- Assessment scheduled → completed → report sent
- Cost estimation sent
- Funding cycle approved
- Associate allocated
- Invoices (VTA + Associate) — sent, paid
- Case notes added
- Discharge

This gives Samy a single-screen audit trail for any patient without navigating between 6 different sections.

### 3.4 "Quick Actions" Panel on Patient Page

A context-aware panel that shows only the **next logical action** based on current status:

| Patient status | Quick action shown |
|---------------|-------------------|
| Enquiry Logged | "Log Response" |
| Response Sent | "Schedule Assessment" |
| Assessment Scheduled | "Record Assessment Outcome" |
| Report Sent | "Create Cost Estimation" |
| Cost Estimation Sent | "Record Funding Approval" |
| Funding Approved | "Allocate Associate" |
| Treatment Active | "Create VTA Invoice" |

This prevents staff from skipping steps and makes the workflow self-documenting.

### 3.5 Document Enforcement at Key Gates

Three hard gates where the system should **block progression without a document**:
1. Cannot mark patient "Funding Approved" without `approval_document_path` on the funding cycle (BR-C1)
2. Cannot mark VTA invoice "Sent" without a document uploaded (BR-F6 — already in spec)
3. Cannot mark assessment "Report Sent" without uploading the assessment report

### 3.6 Associate Allocation with Notification

When an associate is allocated to a patient:
- Log a communication entry automatically ("Associate [name] allocated on [date]")
- Show the allocation on the patient timeline
- Optionally: trigger an email notification to the associate (future phase)

### 3.7 Funding Balance Visibility

On the patient page, show a **live funding summary**:
- Approved amount (from active funding cycle)
- Invoiced to date (sum of all VTA invoices sent/paid)
- Remaining balance
- % used (progress bar)

`FundingBalanceService` already computes this — it just needs to be surfaced on the patient page.

### 3.8 Email Intake — Tag Emails to Records

The `email_intake_logs` table exists. Improve it so each email can be tagged to a:
- Patient (patient_id)
- Enquiry (enquiry_id)
- VTA Invoice (vta_invoice_id)
- Funding Cycle (funding_cycle_id)

This makes the "Emails" nav section useful — staff can see all emails related to a patient from the patient page, and all untagged emails in the Emails dashboard widget.

### 3.9 Reports Section — Phase 1 (4 basic reports)

Build these as pre-written database queries, no complex reporting engine needed:

| Report | What it shows |
|--------|--------------|
| Active Patients by Status | Count of patients in each status, filterable by date range |
| Funding Balance Summary | All active funding cycles: approved vs invoiced vs remaining |
| Invoice Ageing | VTA invoices by status (Draft/Sent/Overdue/Paid) with totals |
| Associate Activity | Sessions/invoices per associate in a date range |

### 3.10 CLAUDE.md / Project Documentation Update

Update `CLAUDE.md` with:
- Correct project path (`C:\xampp\htdocs\VTA_NEW\vta-portal` not `C:\xampp\htdocs\vta-portal`)
- Add `enquiry_id` FK on patients to the data model section
- Add Assessment table to the architecture
- Add "Qualified as Referral" as a formal status
- Add the 5 dashboard widgets requirement

---

## Appendix — Files Reviewed Today

| File | Key Findings |
|------|-------------|
| `CamScanner 26-06-2026 22.54.pdf` | Enquiry form design: multiple enquirers, 4 follow-ups, approval status, link to patient |
| `CamScanner 26-06-2026 22.55.pdf` | Patient form design: Next of Kin, referrer roles, assessment section, cost figures, approval status |
| `Actions frommeeting on 25th June.pptx` | Dashboard: 5 widgets. Nav: Companies, Associates, Emails, Reports, Core/Supportive Processes |
| `Process mapping.xlsx` | Samy's live Excel: 32-column end-to-end tracker confirming all field names, 4 follow-ups, "Qualified as Referral" gate, assessment financials |
| Live code review | Enquiry→Patient link missing, no qualification gate, funding approval unenforced, document upload gap on funding cycle form |
