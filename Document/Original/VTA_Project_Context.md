# VTA Portal — Project Context & Decision Log
## Vestibular Therapy Associates — Complete Background for Any Agent or Developer
### Version 1.0 | June 2026 | Read This Before Touching Any Code

---

## CRITICAL INSTRUCTION

This document must be read BEFORE reading the spec, before writing any code, and before making any decisions about how the system works. It explains WHO VTA is, WHY this portal exists, WHAT was tried before, and WHY every major decision was made. Without this context, an agent or developer will make wrong assumptions.

---

## PART 1 — ABOUT VESTIBULAR THERAPY ASSOCIATES (VTA)

### 1.1 What VTA Does
Vestibular Therapy Associates is a UK-based specialist vestibular therapy practice founded and directed by Dr. Samy Selvanayagam. The practice provides assessment and treatment for patients with vestibular disorders — primarily dizziness, balance problems and related conditions, often associated with Traumatic Brain Injury (TBI).

VTA does not operate a traditional clinic. Instead, Samy manages a network of specialist associate physiotherapists who travel to patients across the UK and deliver treatment in the patient's home or agreed location.

### 1.2 How VTA Generates Business
VTA markets its services offline through professional networks, LinkedIn and word of mouth. Enquiries arrive via:
- Email (primary channel)
- LinkedIn messages
- Phone calls directly to Samy
- Referral letters (postal or GP letters)

VTA does NOT have a public-facing booking system. All referrals come through professional intermediaries.

### 1.3 The People at VTA
| Person | Role | System Access |
|---|---|---|
| Dr. Samy Selvanayagam | Founder, Director, Clinical Supervisor | Admin — full access to everything |
| Sheeba Rossewilliam | Operations Staff | Staff — daily referral management |
| Jai Anand (HariKrishnan JaiAnand) | IT Solutions Implementor | Admin — system builder and maintainer |

**Important:** Jai is not a VTA employee. He is an external IT solutions implementor who built this system. His email is jai@vestibulartherapyassociates.co.uk but he operates as the technical lead, not operations staff.

### 1.4 The Associates (External Clinicians)
Associates are specialist physiotherapists contracted by VTA to deliver treatment. They are NOT VTA employees. They invoice VTA for their work and VTA invoices the funder. All 9 associates:

| Name | Region | Speciality |
|---|---|---|
| Kate Bryce | North East England | Falls and Balance Rehabilitation |
| Anna Bennett | Yorkshire | Advanced Vestibular Physiotherapy |
| Lewis Brennan | London and Cambridgeshire | Musculoskeletal and Vestibular Rehabilitation |
| Georgios Tsiknas | West Midlands | Specialist Vestibular Physiotherapy |
| Ileana Dascalu | London | Paediatric and Adult Rehabilitation |
| Nick Hill | North West England | Specialist Vestibular Physiotherapy |
| Sultana Parvin | Manchester | Specialist Vestibular Physiotherapy |
| Sahash Palanisamy | Dorset | Specialist Vestibular Physiotherapy |
| Samy Selvanayagam | Nationwide | Consultant Vestibular Physiotherapy (Samy himself) |

**Note:** Samy is listed as an associate because he sometimes personally conducts assessments before handing over to another associate for treatment. His associate record links to his admin user account.

### 1.5 The Referrer Hierarchy
Every patient comes through this chain:

```
Company (e.g. Harrison Associates, Irwin Mitchell, Community Case Management)
    └── Case Manager (employed by the company, manages individual client cases)
            └── Patient (the person needing vestibular therapy)
                    └── Funder (who approves and pays — often different from the company)
```

**Companies are of these types:**
- Case Management Companies (e.g. Harrison Associates, Links, Community Case Management, Ben Holden, Westcountry, Bridge)
- Law Firms (e.g. Irwin Mitchell, Fieldfisher, FBC Manby Bowdler)
- Solicitors
- Insurance Companies
- Individuals (rare — some enquiries come directly)

**Funder is separate from Referrer:** The company who refers the patient is often not the one who pays. Payment may come from:
- The case management company directly
- A solicitor acting for the patient
- An insurance company
- Sometimes the patient themselves

This is why the patients table has a separate invoice_recipient_type, invoice_recipient_name and invoice_recipient_email — the invoice does not always go to the case manager's company.

### 1.6 Scale of Operations
- Approximately 80 active companies
- Approximately 300+ case managers across those companies
- Approximately 20 new patients per month
- Multiple funding cycles per patient (average 2-3 over a patient's lifetime with VTA)
- Each associate may be working with multiple patients simultaneously

---

## PART 2 — WHAT VTA'S CURRENT SYSTEMS LOOK LIKE (BEFORE THE PORTAL)

### 2.1 The Six Systems Samy Uses Today
Samy currently manages everything across SIX separate systems. There is no integration between them. Staff must switch between all six to manage a single patient.

| System | What It Does | Problem |
|---|---|---|
| Excel (TBITA Referral Database) | 35-column spreadsheet — one row per patient tracking everything from first enquiry to discharge | No shared access. Cannot be used by associates. Becomes unmanageable at scale. |
| TBITA Master Folders | Desktop folder structure on Samy's computer: Company → Case Manager → Patient | Only accessible from Samy's desktop. Associates get documents via WhatsApp. Not searchable. |
| Qunote | Clinical platform for appointments and case notes | Completely separate from operations. Associates use it for clinical records but it has no link to the business workflow. |
| Xero | Accounting and invoicing | No case context. Cannot validate invoice against funding approval. No link to patient records. |
| Email (Shared Mailbox) | All inbound referral emails | No workflow. Emails must be manually processed and logged in Excel separately. |
| WhatsApp / Dropbox | Sharing documents with associates and solicitors | Completely informal. No audit trail. Documents shared outside the system. |

### 2.2 The Excel File Structure
Samy's Excel tracker has exactly 35 columns tracking the following across one row per patient:

**Columns 1-12 (Enquiry and Referrer):**
Name of enquirer, Email, Role, Case Manager Name, Company, How They Heard About Us, Reason for referral, Solicitor/Funder, Mode of initial Contact, Client area/location, Initial enquiry date, First response

**Columns 13-18 (Follow-ups):**
Nearest Associate, Follow up 1 Date, Follow up 2 Date, Follow up 3 Date, Follow up 4 Date, Remarks

**Columns 19-28 (Patient and Clinical):**
Date qualified, Patient details, Funding/Invoice to, Solicitors/Insurance, Fee for Initial Ax agreed with Referrer, Date client was contacted, Initial Assessment date, Clinician Initial Assessment, Clinician Treatment, Mentor/Clinical Supervisor

**Columns 29-35 (Funding and Sessions):**
No of sessions agreed, Funding agreed Phase 1, Funding agreed Phase 2, Funding agreed Phase 3, Total no of sessions completed, Remarks, Lessons learnt

**The Excel file shared with us (VTA_Key_Data_Master_.xlsx) was a template with no actual data rows.** Samy confirmed data will be entered manually at go-live — no automated migration needed.

### 2.3 The Folder Structure Samy Uses
```
TBITA Master (on Samy's desktop)
└── [Company Name]/
        └── [Case Manager Name]/
                ├── NDA + Materials (sent at enquiry stage)
                └── [Patient Name]/
                        ├── LOI
                        ├── Medical Records
                        ├── Assessment Reports
                        ├── Cost Estimations
                        ├── Invoices
                        └── Correspondence
```

The VTA Portal replicates this exact structure in its file storage system at:
`storage/app/vta-documents/{company-slug}/{case-manager-slug}/{patient-slug}/`

### 2.4 The 19-Step Workflow (As It Happens Today)
All 19 steps are done manually by Samy, with no system enforcing sequence or gates:

**Phase A — Intake and Onboarding:**
1. Enquiry received (by email, LinkedIn, phone or letter)
2. Logged in Excel spreadsheet
3. Response sent (with cost estimation, CVs, T&Cs)
4. Folder created on desktop (Company/Case Manager)
5. NDA received and filed, materials sent
6. Letter of Instruction (LOI) received

**Phase B — Clinical Setup:**
7a. Patient set up on Qunote
7b. Appointment booked with associate

**Phase C — Assessment and Reporting:**
8. Assessment conducted by associate
9. Report drafted (by associate, supervised by Samy)
10. Report sent to case manager (sometimes password-protected)
11. Invoice raised for assessment
12. Submitted to funder

**Phase D — Treatment Delivery:**
13. Funding approval received (written — MUST be on file before treatment starts)
14. Treatment delivered by associate
15. Monthly invoicing to funder

**Phase E — Finance, Records and Discharge:**
16. Xero reconciliation
17. Records requests handled
18. Patient discharged
19. Case close summary created

---

## PART 3 — WHAT WAS TRIED BEFORE THE PORTAL

### 3.1 The Microsoft 365 Attempt
Before deciding on a dedicated portal, a full Microsoft 365 implementation was attempted. This work is documented here so no future agent or developer tries to revisit it.

**What was set up in M365:**
- Microsoft Teams — Vestibular Therapy Associates team created with 5 channels: General, Appointments (with Planner board), Management (private), New Referrals (with Lists tab), Partner Doctors
- Shared Mailbox — operations@vestibulartherapyassociates.co.uk created in Exchange Admin Centre with Jai, Samy and Sheeba as members
- SharePoint Document Library — "Referral Documents" with Referrals, Templates and Archive folders
- Microsoft Lists — "Patient Referral Master" with 11 columns
- Microsoft Planner — "VTA Referral Workflow" with 7 buckets (New, Under Review, Assigned, Awaiting Docs, Appointment, Treatment, Completed)
- Power Automate Flow — "VTA Referral Intake" — triggered on email to shared mailbox, saved attachments to SharePoint, created list record, created Planner task, posted Teams notification
- Power Apps — "VTA Allocation Screen" — connected to Patient Referral Master and VTA Doctors list, showing doctor matching based on patient location
- VTA Doctors List — SharePoint list with all 9 associates

**Planner Bucket IDs (for reference only — no longer used):**
- New: RXFzGoJmBUiQ7he9ajcG9JgAKf1u
- Under Review: iuRdkOy5pE6qQz9qY2Gw6ZgAHWOw
- Assigned: cuM6m3jQkkaVX2UBY6OxFpgAK1TP
- Awaiting Docs: nKwnRBILvkiF7ubNcKaK-5gACfST
- Appointment: dAsQiBqgIEOa6I54UT7akJgAAoIq
- Treatment: gj5Oh9mLYkawKUKlS3FrXZgAAKfj
- Completed: B4pmFki4K06TPOYCjFKs8ZgAKY4y

**The M365 trial is still active but should be cancelled.** It has not been paid for. No critical data lives there. The work done was essentially a proof of concept that proved the approach was wrong.

### 3.2 Why M365 Was Abandoned
The decision to abandon M365 was made after full analysis. The reasons:

**Cannot replace the three systems VTA needs to replace:**
- Cannot replace Qunote — no appointment calendar, no clinical notes system in M365
- Cannot replace Xero — no invoicing capability in M365
- Cannot replace the Excel tracker at the depth required

**Cannot serve external users at scale:**
- Associates as M365 guests require individual licences — costly and complex to manage for 8+ people
- Case managers (300+) cannot be given M365 access at any reasonable cost
- Patients cannot access M365 at all, ever

**Cost problem:**
- M365 is in trial. After trial: minimum £30-90/month for 3 users
- Every new associate, staff member or case manager adds licence cost
- Over 3 years: £1,080-3,960 with major limitations still in place

**Technical limitations:**
- Power Apps has hard limits — no proper calendar, no invoicing, no external portals
- Power Automate Planner sync flow failed and had to be deleted — manual status update agreed as workaround
- The "Doctor Assigned" field in M365 Lists is a Person field (requires M365 account) — external associates cannot be stored there, so a workaround "Allocated Doctor" text field was needed
- SharePoint documents are inaccessible to anyone without an M365 account

**The M365 work was not wasted** — it proved exactly what VTA needs, revealed all the edge cases, and the workflow and data model designed for M365 went directly into the portal specification.

### 3.3 The DizzyCare Wireframes
Early in the project, a solution design document was created called "DizzyCare — Complete Solution Design Document" hosted at easyerp.co.in/DizzyCare_Wireframes.html. This described a 7-layer M365 architecture and 5 wireframe screens. This document is superseded by everything in the portal specification. Do not reference it for any decisions.

---

## PART 4 — TECHNOLOGY DECISIONS AND REASONING

### 4.1 Why a Dedicated Portal (Not M365, Not Hybrid)

**Three options were evaluated:**

**Option A — Pure M365:** Cannot replace Qunote or Xero. Cannot serve external users. Dead end within 12-18 months.

**Option B — Hybrid (M365 + Custom App):** Pays for M365 without getting meaningful value from it. Two systems to maintain. Documents in SharePoint still inaccessible to case managers and patients. More complex than either option alone.

**Option C — Dedicated Portal (chosen):** One system replaces all three (Excel, Qunote, Xero invoicing). All user types get proper access. No per-user licensing. Scales to 300+ case managers at zero extra cost. UK-hosted with healthcare-grade security. £19/month fixed forever.

### 4.2 Why Laravel (Not .NET Core, Not React standalone)

**.NET Core was initially in the specification** but was replaced by Laravel for these reasons:
- Krystal Emerald is a PHP shared hosting environment — .NET is not natively supported
- Laravel runs natively on Krystal with no extra setup
- Laravel development speed is faster — Eloquent ORM, built-in auth, built-in mail, built-in queue
- Livewire enables reactive UI without needing a separate Node.js server
- More accessible for future developers if Jai is unavailable

### 4.3 Why MySQL (Not PostgreSQL)

PostgreSQL was considered but MySQL was chosen because:
- Krystal Emerald provides MySQL — PostgreSQL is not available on shared hosting
- Using PostgreSQL would require a VPS, increasing cost and complexity
- Laravel works equally well with both — no functional difference for this application
- VTA's data model is straightforward relational — no PostgreSQL-specific features needed
- phpMyAdmin included in Krystal cPanel — Samy can view data without developer help

**If VTA outgrows Krystal in 2-3 years and moves to a VPS, migrating to PostgreSQL is straightforward** because Laravel makes database switching easy.

### 4.4 Why Krystal Emerald (Not Samy's Existing VPS)

Samy had an existing VPS. Krystal Emerald was chosen over it because:
- ISO 27001 certified (important for healthcare patient data)
- PCI DSS compliant
- Automatic backups every 4 hours (VPS backups are manual)
- 99.99% uptime guarantee
- UK-based (GDPR compliance for patient data)
- DDoS protection (2,000 Gbps) included
- cPanel makes management accessible to non-technical users
- Unlimited NVMe storage — no concerns about document storage growth
- £19/month — all-in, no hidden costs
- Unlimited email addresses included

### 4.5 Why No Public Registration

The portal has no public registration. All accounts are created by Samy or Jai in the Settings screen. Reasons:
- This is a clinical system handling patient data — public registration would be a serious security risk
- All user types (associates, case managers, patients) have pre-existing relationships with VTA before they need access
- Associates are onboarded by Samy personally — their account is created when they are contracted
- Case managers (Phase 4) will be given access after their case manager record already exists
- A login page message directs anyone without an account to contact operations@

### 4.6 Why Subdomain (Not Main Domain or Path)

The portal runs at portal.vestibulartherapyassociates.co.uk (not the root domain). Reasons:
- The existing VTA website at vestibulartherapyassociates.co.uk is a public marketing site — should not be touched
- Mixing a public marketing site with a private clinical system on the same domain creates technical and security risks
- Subdomain setup is a 5-minute operation in Krystal cPanel
- SSL certificate for the subdomain is free and auto-renewing via Let's Encrypt
- The main website simply adds a small "Staff Portal" link pointing to the subdomain — no other changes needed

---

## PART 5 — SAMY'S DIRECT ANSWERS TO ALL QUESTIONS

These are the exact answers provided by Dr. Samy Selvanayagam to questions asked during the requirements gathering process. These are authoritative — they override any assumptions in the spec if there is a conflict.

### Q1: When a new enquiry arrives, do you always create a Company and Case Manager record first?
**Samy's Answer:** First, log the enquiry into the spreadsheet database. I always create folders (Company, Case Manager and Client). One patient might have been referred by a Case Manager but then moved to a different Case Manager within the same company, or the Case Manager takes the client to a different company. But these do not happen very often. When it happens I make changes as needed.

**What this means for the portal:**
- The portal has a two-stage enquiry process: quick log first, then convert to full records
- Patient transfer between case managers must be supported (with history tracking)
- Patient following a case manager to a different company must also be supported

### Q2: Can one patient have more than one associate across their treatment?
**Samy's Answer:** Yes, that is always a possibility. For example, I might complete the Initial Assessment and then hand over to an Associate.

**What this means for the portal:**
- One patient can have multiple associates
- Samy himself (as an associate) may do the initial assessment
- The patient_associates table has a role field (Assessment, Treatment, Supervision, MDT)
- Only one associate per role type active at a time (e.g. one Treatment associate)
- Associates are given a start_date and end_date — end_date NULL means currently active

### Q3: How does the funding phase system work?
**Samy's Answer:** First quote/cost estimation is when they enquire for initial assessment. Second quote is issued when initial assessment is completed and this cost estimation is considered as Phase 1. When they approve, we have the agreed amount to issue the invoices. Following this, when the funding dries up but client needs ongoing treatments, further funding is requested with another cost estimation — this will be Phase 2. It goes on like this until the client is discharged. Usually each cost estimation is for 3-6 months but sometimes it is issued for 12 months. Every time we get approval, we need to keep a track of how much is remaining available after each invoice is issued.

**What this means for the portal:**
- Funding cycles are sequential and open-ended — there is no fixed number of phases
- Each cycle starts with a new cost estimation
- Each cycle has its own approved amount and approved sessions
- The remaining balance of each cycle must be tracked: approved_amount minus sum of paid VTA invoices for that cycle
- A patient can have 1, 2, 3 or more funding cycles over their treatment lifetime
- The portal shows a progress bar and remaining balance for the active cycle

### Q4: Does the portal need to generate and store report passwords?
**Samy's Answer:** I currently just create a password, share and store it.

**What this means for the portal:**
- The portal stores the password (encrypted using Laravel's encrypt() helper)
- Also stores when it was shared and how it was shared (Email, WhatsApp, Post, Other)
- A checkbox flags whether the document is password-protected
- Only admins can view the stored password

### Q5: How do associates submit invoices?
**Samy's Answer:** The Associates submit their invoices via email. I then have to use the info supplied by the Associate to create a customer invoice.

**What this means for the portal:**
- Associates email their invoices to Samy
- Staff log the invoice details manually in the portal (associate_invoices table)
- Samy then uses that logged information to create the outgoing VTA invoice to the funder (vta_invoices table)
- The associate portal does NOT have an invoice submission form — associates still email their invoices

### Q6: Does each associate have one standard rate or different rates?
**Samy's Answer:** The rates would be different from Associate to Associate.

**What this means for the portal:**
- Each associate has their own session_rate (fixed fee per session) and travel_rate_per_mile
- These are set in Settings → Associates
- When staff log an associate invoice, the expected amount is auto-calculated based on sessions × rate and miles × travel rate
- The calculated amount is editable — staff can override if the associate invoiced a different amount

### Q7: Is the VTA invoice always sent to the funder?
**Samy's Answer:** Variable — sometimes to the Case Manager, other times to the solicitor, and sometimes to an insurance company.

**What this means for the portal:**
- Each patient record has: invoice_recipient_type, invoice_recipient_name, invoice_recipient_email, invoice_recipient_address
- These are set on the patient record and auto-filled when creating a VTA invoice (editable per invoice)
- Options: Case Manager Company, Solicitor, Insurance Company, Other

### Q8: Should some documents be internal only and others visible to case managers?
**Samy's Answer:** That is correct.

**What this means for the portal:**
- document_type_permissions table controls which document types each role can see
- Samy manages this in Settings → Document Permissions
- A visual matrix shows document types as rows and roles (case manager, associate, patient) as columns
- Samy toggles ON/OFF for each combination
- Default permissions are set in the seeder but Samy can change them at any time

### Q9: Can the same document belong to both a case manager and a patient?
**Samy's Answer:** I do not quite understand this question.

**What this means for the portal:**
- This question was simplified in the implementation
- When uploading a document to a patient record, staff see a toggle: "Also link to Case Manager?"
- If YES, the document appears in both the patient's document list and the case manager's document list
- Technically: documents table has both patient_id and case_manager_id columns — both can be set simultaneously

### Q10: Does the calendar need to show all associates or just per-patient?
**Samy's Answer:** Management/Admin Team should have an overview of the total calendar view of all the activities taking place in the company — Associates activities, Senior team members activities etc. But the Associates can only have access to their own calendar.

**What this means for the portal:**
- Admin and Staff → full team calendar (FullCalendar.js) showing all associates and Samy
- Associate portal → own appointments only (filtered FullCalendar.js view)
- Calendar shows: associate appointments, Samy's supervision sessions, MDT calls, report deadlines
- Colour-coded by associate on the team view

### Q11: Associate rates — session rate or hourly?
**Answer confirmed:** Fixed session rate plus a separate travel rate per mile.

**What this means for the portal:**
- associates.session_rate = fixed fee per session (e.g. £95.00 per session)
- associates.travel_rate_per_mile = pence/pounds per mile (e.g. £0.45 per mile)
- When logging an associate invoice: staff enter sessions completed + miles travelled → portal calculates expected amount

### Q12: Activity types for billing — fixed list or dynamic?
**Answer:** Make it dynamic — it may increase in future.

**What this means for the portal:**
- activity_types is a database table managed in Settings
- Initial seed data: Initial Assessment, Treatment Session, Report Writing, MDT Call, Home Visit, Supervision Session, Travel
- Samy can add new types, edit existing ones, or deactivate ones no longer used
- Deactivated types disappear from dropdowns but historical records are preserved

### Q13: Document types — fixed list or dynamic?
**Answer:** Make it dynamic — it may increase in future.

**What this means for the portal:**
- document_types is a database table managed in Settings
- Initial seed data: LOI, INA, Medical Records, Cost Estimation, Assessment Report, Funding Approval, Associate Invoice, VTA Invoice, Case Close Summary, NDA, Brochure/Materials, Progress Report, Correspondence
- Samy can add new types at any time

### Q14: Which documents can case managers see?
**Answer:** Samy wants to control this himself through the settings.

**What this means for the portal:**
- No hardcoded document visibility — all controlled through document_type_permissions table
- Samy manages in Settings → Document Permissions matrix
- Default suggested permissions (Samy can change):
  - Case Manager CAN see: LOI, Assessment Report, Cost Estimation, VTA Invoice, Funding Approval, Case Close Summary, NDA, Brochure/Materials
  - Case Manager CANNOT see: INA, Medical Records, Associate Invoice
  - Associate CAN see: LOI, INA, Medical Records, Assessment Report, Funding Approval
  - Associate CANNOT see: Associate Invoice, VTA Invoice, Case Close Summary

### Q15: What activity types appear on the calendar?
**Answer:** All of them — Associate assessments, treatment sessions, report deadlines, MDT calls, Samy's supervision sessions, admin tasks.

**What this means for the portal:**
- All appointments regardless of activity_type appear on the team calendar
- Colour-coded by associate (each associate has a distinct colour)
- Samy's own sessions appear in his colour
- Events are clickable to show detail popup

### Q16: Data migration from existing systems?
**Answer:** Manually enter current active cases at go-live.

**What this means for the portal:**
- No automated data import tool required
- No migration from Excel, Qunote or Xero
- At go-live, Samy and Sheeba will manually enter active cases
- The Getting Started guide inside the portal will walk them through this
- Historical closed cases stay in Excel — they are not migrated

### Q17: Two-stage enquiry process?
**Answer:** Option A — log a quick enquiry first, then convert to full record when it progresses.

**What this means for the portal:**
- Enquiries table for quick logging (name, company name as free text, source, date, notes)
- Status: New → In Progress → Converted → Not Proceeding
- "Convert to Full Record" button creates Company + Case Manager records
- Links enquiry to the created records for audit trail

### Q18: Should Samy's own activities appear on the team calendar?
**Answer:** Yes.

**What this means for the portal:**
- Samy has an associate record (Samy Selvanayagam, Nationwide)
- His supervision sessions and MDT calls are logged as appointments
- They appear on the team calendar in his colour alongside other associates

---

## PART 6 — KEY DESIGN DECISIONS MADE DURING DEVELOPMENT

### 6.1 The "Needs Review" System
Every new patient record and every new email intake log is automatically flagged needs_review = TRUE. Staff work through the Daily Actions view (needs_review = TRUE only) and set it to FALSE when they have actioned each record. This makes it disappear from the daily view without deleting it. The record remains in All Items / full history views.

**Why this approach:** Samy and Sheeba needed a way to see only what requires attention today — not the full list of hundreds of records. This flag-based system is the simplest possible implementation and requires no complex filtering logic.

### 6.2 The Funding Cycle Gate
A patient's status cannot move to "Funding Approved" unless a funding_cycle record exists with an approval document uploaded. This is enforced in the backend (PatientController) not just the UI. The reason: Samy was explicit that no treatment should ever start without written funding approval on file. This is both a business rule and a clinical risk management requirement.

### 6.3 Invoice Exceeding Funding Balance — Warning Not Block
When a VTA invoice total exceeds the remaining funding cycle balance, the portal shows an amber warning but does NOT prevent saving. The reason: Samy may sometimes legitimately invoice slightly above the agreed amount (e.g. for additional sessions approved verbally). He needs to be warned, not blocked. A mandatory notes field appears explaining the override.

### 6.4 The Document Permission Matrix
Rather than hardcoding which document types each role can see, this is controlled by Samy in Settings. The reason: Samy could not definitively answer which documents case managers should see — he wanted to control this himself and change it over time. A permission matrix (document types × roles with on/off toggles) gives him that control without needing developer intervention.

### 6.5 Activity Types and Document Types as Dynamic Tables
Both are database-managed lookup tables rather than hardcoded enum values. The reason: Samy explicitly said both may increase in future as VTA's services expand. Making them dynamic means Samy can add "Video Consultation" or "Home Assessment Report" at any time from the Settings screen without any code changes.

### 6.6 Associate Rates — Session Rate + Travel Rate
Associates are paid a fixed fee per session (not hourly) plus a per-mile travel rate. These are stored on the associate record. When staff log an associate invoice, the expected amount is auto-calculated. Staff can override the calculated amount if the actual invoice differs. The reason for editable calculation: some sessions may be different lengths, or travel may be invoiced differently by some associates.

### 6.7 The Email Intake Approach
All emails arriving at operations@ are automatically read every 15 minutes via IMAP and stored in email_intake_logs. Staff then review the list and:
- Link the email to an existing patient (attaches it to their record)
- Create a new patient from the email (pre-fills the patient form)
- Link to a case manager (for enquiry-stage emails)
- Mark as irrelevant (spam, internal, non-referral)

**Why this approach instead of automated categorisation:** Samy forwards real referral emails to operations@ from his personal email. The "From" address on these forwarded emails is always samy@vestibulartherapyassociates.co.uk — not the original sender. This makes automatic patient matching impossible. Manual review by staff is the most reliable approach.

**The duplicate email problem:** When Samy forwards an email about an existing patient, the system creates a new record. Staff must:
1. Identify the existing patient
2. Link the new email to them
3. Move any documents to the correct patient folder
4. Delete the duplicate intake log entry

This is expected behaviour, documented for staff.

### 6.8 No Status Sync Between Planner and Lists
During the M365 phase, a Power Automate flow was built to sync Planner card positions to List status updates. This flow failed due to Planner API limitations and was deleted. Manual status update was agreed as the approach — staff update both Planner (drag card) and List (change status dropdown). In the dedicated portal, there is no Planner — status is managed directly on the patient record.

---

## PART 7 — EDGE CASES AND HOW THEY ARE HANDLED

### 7.1 Patient Changes Case Manager (Within Same Company)
- Staff click "Transfer to Different Case Manager" on the patient detail
- A modal opens: search and select new case manager
- Reason field is required
- On save: patient_case_manager_history record created (previous CM, new CM, date, reason)
- patients.case_manager_id updated to new case manager
- All existing documents, appointments, case notes and communications remain intact
- The old case manager's patient list no longer shows this patient
- The new case manager's patient list shows this patient

### 7.2 Patient Follows Case Manager to Different Company
Same process as above — the new case manager may be at a different company. The transfer process handles this: staff select the new case manager (who may be at a different company). The history record captures both the previous company and new company.

### 7.3 Duplicate Email Records
When Samy forwards an email and a duplicate intake log is created for an existing patient:
- Staff review email intake daily
- Identify the email belongs to an existing patient
- Link it to that patient
- Move any attachments from inbox folder to correct patient folder manually
- Process the original duplicate record

This takes approximately 1-2 minutes per duplicate. Given the low volume of referrals (~20/month) this is acceptable.

### 7.4 Invoice Exceeds Funding Cycle Balance
- Portal shows amber warning (BR-F1) but does not block
- Staff must add a note explaining the override
- Invoice is saved with a flag indicating manual override
- Samy is notified via the warning on the invoice form

### 7.5 Multiple Funding Cycles Per Patient
A patient's funding does not stop at Phase 3 — it can continue indefinitely. Each new cycle is a fresh cost estimation, sent to the funder, approved, and tracked. The funding_cycles table has cycle_number auto-incremented per patient. When a cycle's funds are nearly exhausted, patient status changes to "Awaiting Further Funding" while a new cost estimation is prepared and sent.

### 7.6 Password-Protected Reports
Samy creates a password when sending a sensitive assessment report. The portal stores this password encrypted. When the case manager eventually gets portal access (Phase 4), they cannot see the password — they would contact VTA to get it separately. The portal just records that a password was used and when it was shared.

### 7.7 Associate Rates Varying by Associate
All 8 external associates have different session rates. Samy negotiates these individually. The rates are stored on the associate record and used to auto-calculate expected invoice amounts. Associates do not see their own rate in the portal.

---

## PART 8 — WHAT IS DELIBERATELY OUT OF SCOPE

These items were considered and explicitly decided against. Do not add them without consulting Samy.

| Item | Why Out of Scope |
|---|---|
| Public registration | Security risk for clinical data. All accounts created by admin. |
| Automated email → patient matching | From address is always Samy's email on forwards — matching is unreliable |
| Automatic Planner-to-List sync | Attempted in M365 phase, failed due to API limitations, manual approach agreed |
| AI Builder email parsing | Standard manual review sufficient at current volume |
| Bank reconciliation | Only invoicing + payment tracking from Xero — not full accounting |
| Report password generation | Samy creates passwords himself, portal just stores them |
| GP / clinical system integration | Qunote data not exportable, fresh start agreed |
| Historical data migration | Samy will manually enter active cases at go-live |
| Case Manager portal | Phase 4 — scaffold routes only for now |
| Patient portal | Phase 5 — not in current development scope |
| Mobile native app | Web app only — mobile responsive browser version sufficient |
| Video consultation platform | Associates use their own tools (Zoom, Teams) — VTA portal just logs the appointment |
| Automated invoice generation to PDF | Manual PDF upload — Samy creates invoices in his preferred tool and uploads |
| SMS notifications | Email notifications only |
| Multi-language support | English only |
| GDPR data export automation | Manual export by admin sufficient at current scale |

---

## PART 9 — PEOPLE, CONTACTS AND RESPONSIBILITIES

### 9.1 Project Stakeholders
| Person | Role | Responsibility |
|---|---|---|
| Dr. Samy Selvanayagam | Client, Owner | Final approval on all decisions. Defines business rules. |
| Jai Anand (HariKrishnan JaiAnand) | IT Solutions Implementor | Builds the system. Manages Krystal hosting. Admin access. |
| Sheeba Rossewilliam | Operations Staff | Primary daily user. First to test new features. |

### 9.2 Key Emails
| Address | Purpose |
|---|---|
| samy@vestibulartherapyassociates.co.uk | Samy's primary email |
| jai@vestibulartherapyassociates.co.uk | Jai's admin email |
| sheeba@vestibulartherapyassociates.co.uk | Sheeba's staff email |
| operations@vestibulartherapyassociates.co.uk | Shared intake mailbox — all referrals arrive here |

### 9.3 Hosting Access
- **Krystal cPanel:** vestibulartherapyassociates.co.uk:2083
- **Portal URL:** portal.vestibulartherapyassociates.co.uk
- **Main website:** vestibulartherapyassociates.co.uk (do not touch — separate from portal)
- **DNS:** Managed at Krystal (kloudns nameservers)

### 9.4 Other Domains in the Tenant (M365 — being cancelled)
During the M365 phase, two additional domains were identified with incomplete M365 setup:
- tbitherapyassociates.co.uk
- dizzycareacademy.co.uk

These are not relevant to the portal. They are VTA-related domains for other ventures.

---

## PART 10 — DOCUMENTS IN THIS PROJECT PACKAGE

The complete project package consists of four documents. All four should be read together.

| Document | File Name | Purpose |
|---|---|---|
| This document | VTA_Project_Context.md | The WHY — history, decisions, Samy's answers, edge cases |
| Master Specification | VTA_Portal_Master_Spec.md | The WHAT — every screen, table, business rule, build order |
| Test Cases | VTA_Portal_Test_Cases.md | The VERIFY — 100 test cases across all phases |
| Session Summary | VTA_Session_Summary.md | The STATUS — current development status |

### Reading Order for a New Agent or Developer
1. Read this document (VTA_Project_Context.md) first — understand the business
2. Read VTA_Portal_Master_Spec.md — understand what to build
3. Check VTA_Session_Summary.md — understand what has already been done
4. Use VTA_Portal_Test_Cases.md — verify what has been built

### How to Give These to Cowork
1. Open Claude Desktop → switch to Cowork
2. Create a Project called "VTA Portal Development"
3. Point Cowork at the project folder: C:\xampp\htdocs\VTA_NEW\
4. Also include the Document folder containing all four markdown files
5. First prompt: "Read all four documents in the Document folder, starting with VTA_Project_Context.md. Then tell me what you understand about the VTA Portal project and what the current state of development is."

---

*Document Version: 1.0*
*Created: June 2026*
*Authors: Jai Anand (IT) based on requirements from Dr. Samy Selvanayagam*
*Application: VTA Portal — Vestibular Therapy Associates*
*This document captures decisions made between approximately April and June 2026*
